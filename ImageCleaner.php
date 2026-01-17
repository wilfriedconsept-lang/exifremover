<?php

/**
 * ImageCleaner - Clean image metadata without re-encoding
 * Preserves image quality by manipulating binary data only
 */
class ImageCleaner {

    /**
     * Clean image metadata based on mode
     *
     * @param string $sourcePath Source image path
     * @param string $destPath Destination path
     * @param string $mimeType Image MIME type
     * @param string $mode 'ai' or 'full'
     * @return bool Success status
     */
    public static function clean($sourcePath, $destPath, $mimeType, $mode = 'full') {
        switch ($mimeType) {
            case 'image/jpeg':
                return self::cleanJPEG($sourcePath, $destPath, $mode);
            case 'image/png':
                return self::cleanPNG($sourcePath, $destPath, $mode);
            case 'image/webp':
                return self::cleanWebP($sourcePath, $destPath, $mode);
            default:
                return false;
        }
    }

    /**
     * Clean JPEG metadata without re-encoding
     * Removes EXIF, IPTC, XMP, and other APP segments
     */
    private static function cleanJPEG($sourcePath, $destPath, $mode) {
        $data = file_get_contents($sourcePath);
        if ($data === false) return false;

        // JPEG starts with FFD8
        if (substr($data, 0, 2) !== "\xFF\xD8") return false;

        $output = "\xFF\xD8"; // SOI (Start of Image)
        $pos = 2;
        $length = strlen($data);

        // AI-specific markers to remove
        $aiMarkers = [
            'C2PA',           // Content Credentials
            'caBX',           // Content Authenticity Box
            'Adobe',          // Adobe XMP (often contains AI generation data)
            'photoshop',
            'Midjourney',
            'DALL-E',
            'Stable Diffusion',
            'Generated',
        ];

        while ($pos < $length) {
            // Check for marker
            if ($data[$pos] !== "\xFF") break;

            $marker = ord($data[$pos + 1]);
            $pos += 2;

            // SOS (Start of Scan) - compressed image data follows
            if ($marker === 0xDA) {
                // Copy from SOS to end (this is the actual image data)
                $output .= "\xFF\xDA" . substr($data, $pos);
                break;
            }

            // Standalone markers (no data segment)
            if ($marker === 0x00 || ($marker >= 0xD0 && $marker <= 0xD9)) {
                $output .= "\xFF" . chr($marker);
                continue;
            }

            // Read segment length
            if ($pos + 2 > $length) break;
            $segmentLength = (ord($data[$pos]) << 8) | ord($data[$pos + 1]);

            if ($pos + $segmentLength > $length) break;

            $segment = substr($data, $pos, $segmentLength);
            $shouldKeep = true;

            if ($mode === 'full') {
                // Full mode: remove ALL metadata segments
                // APP0-APP15 (0xE0-0xEF) contain metadata
                // COM (0xFE) contains comments
                if (($marker >= 0xE0 && $marker <= 0xEF) || $marker === 0xFE) {
                    // Exception: Keep APP0 (JFIF) for compatibility
                    if ($marker === 0xE0 && strpos($segment, 'JFIF') !== false) {
                        $shouldKeep = true;
                    } else {
                        $shouldKeep = false;
                    }
                }
            } else {
                // AI mode: selectively remove AI-related metadata
                $segmentContent = strtolower($segment);

                // Check if segment contains AI markers
                foreach ($aiMarkers as $aiMarker) {
                    if (stripos($segment, $aiMarker) !== false) {
                        $shouldKeep = false;
                        break;
                    }
                }

                // Remove XMP metadata (often contains AI generation params)
                if ($marker === 0xE1 && strpos($segment, 'http://ns.adobe.com/xap') !== false) {
                    $shouldKeep = false;
                }

                // Remove Photoshop IRB (may contain AI tags)
                if ($marker === 0xED) {
                    $shouldKeep = false;
                }
            }

            if ($shouldKeep) {
                $output .= "\xFF" . chr($marker) . $segment;
            }

            $pos += $segmentLength;
        }

        return file_put_contents($destPath, $output) !== false;
    }

    /**
     * Clean PNG metadata without re-encoding
     * Removes tEXt, iTXt, zTXt, and other metadata chunks
     */
    private static function cleanPNG($sourcePath, $destPath, $mode) {
        $data = file_get_contents($sourcePath);
        if ($data === false) return false;

        // PNG signature
        $signature = "\x89PNG\x0D\x0A\x1A\x0A";
        if (substr($data, 0, 8) !== $signature) return false;

        $output = $signature;
        $pos = 8;
        $length = strlen($data);

        // Critical chunks that must be kept
        $criticalChunks = ['IHDR', 'PLTE', 'IDAT', 'IEND'];

        // Chunks to always keep for image integrity
        $keepChunks = ['IHDR', 'PLTE', 'IDAT', 'IEND', 'tRNS', 'gAMA', 'cHRM', 'sRGB'];

        // Metadata chunks to remove
        $metadataChunks = ['tEXt', 'zTXt', 'iTXt', 'tIME', 'pHYs', 'sPLT', 'iCCP', 'eXIf'];

        while ($pos < $length) {
            if ($pos + 12 > $length) break;

            // Read chunk length (4 bytes, big-endian)
            $chunkLength = unpack('N', substr($data, $pos, 4))[1];
            $chunkType = substr($data, $pos + 4, 4);

            // Total chunk size: length(4) + type(4) + data(length) + crc(4)
            $totalChunkSize = 12 + $chunkLength;

            if ($pos + $totalChunkSize > $length) break;

            $shouldKeep = true;

            if ($mode === 'full') {
                // Remove all metadata chunks
                if (in_array($chunkType, $metadataChunks)) {
                    $shouldKeep = false;
                }
            } else {
                // AI mode: remove specific metadata that might contain AI info
                if ($chunkType === 'eXIf' || $chunkType === 'tEXt' ||
                    $chunkType === 'iTXt' || $chunkType === 'zTXt') {

                    // Check if chunk contains AI-related keywords
                    $chunkData = substr($data, $pos + 8, $chunkLength);
                    $aiKeywords = ['AI', 'Generated', 'C2PA', 'Midjourney', 'DALL', 'Stable'];

                    foreach ($aiKeywords as $keyword) {
                        if (stripos($chunkData, $keyword) !== false) {
                            $shouldKeep = false;
                            break;
                        }
                    }
                }
            }

            // Always keep critical chunks
            if (in_array($chunkType, $criticalChunks)) {
                $shouldKeep = true;
            }

            if ($shouldKeep) {
                $output .= substr($data, $pos, $totalChunkSize);
            }

            $pos += $totalChunkSize;

            // Stop after IEND
            if ($chunkType === 'IEND') break;
        }

        return file_put_contents($destPath, $output) !== false;
    }

    /**
     * Clean WebP metadata
     * For WebP, we need to parse the RIFF container format
     */
    private static function cleanWebP($sourcePath, $destPath, $mode) {
        $data = file_get_contents($sourcePath);
        if ($data === false) return false;

        // WebP starts with "RIFF....WEBP"
        if (substr($data, 0, 4) !== 'RIFF' || substr($data, 8, 4) !== 'WEBP') {
            return false;
        }

        // For now, for WebP we'll use a simpler approach:
        // Copy the file and remove EXIF/XMP chunks if present

        $pos = 12; // After RIFF header
        $length = strlen($data);
        $output = substr($data, 0, 12); // Keep RIFF header

        while ($pos < $length) {
            if ($pos + 8 > $length) break;

            $chunkHeader = substr($data, $pos, 4);
            $chunkSize = unpack('V', substr($data, $pos + 4, 4))[1];

            // Pad to even length
            $paddedSize = ($chunkSize + 1) & ~1;
            $totalSize = 8 + $paddedSize;

            if ($pos + $totalSize > $length) break;

            $shouldKeep = true;

            // Remove EXIF and XMP chunks
            if ($mode === 'full' || $mode === 'ai') {
                if ($chunkHeader === 'EXIF' || $chunkHeader === 'XMP ') {
                    $shouldKeep = false;
                }
            }

            if ($shouldKeep) {
                $output .= substr($data, $pos, $totalSize);
            }

            $pos += $totalSize;
        }

        // Update RIFF file size
        $fileSize = strlen($output) - 8;
        $output = substr($output, 0, 4) . pack('V', $fileSize) . substr($output, 8);

        return file_put_contents($destPath, $output) !== false;
    }

    /**
     * Get file size comparison
     */
    public static function getSizeComparison($originalPath, $cleanedPath) {
        $originalSize = filesize($originalPath);
        $cleanedSize = filesize($cleanedPath);

        return [
            'original' => $originalSize,
            'cleaned' => $cleanedSize,
            'saved' => $originalSize - $cleanedSize,
            'percentage' => $originalSize > 0 ? round((1 - $cleanedSize / $originalSize) * 100, 2) : 0
        ];
    }
}
