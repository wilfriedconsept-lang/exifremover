<?php
header('Content-Type: application/json');

// Load ImageCleaner class
require_once __DIR__ . '/ImageCleaner.php';

// Configuration
define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('CLEANED_DIR', __DIR__ . '/cleaned/');
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB
define('ALLOWED_TYPES', ['image/jpeg', 'image/png', 'image/webp']);

// Create directories if they don't exist
if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0755, true);
if (!is_dir(CLEANED_DIR)) mkdir(CLEANED_DIR, 0755, true);

// Clean old files (older than 1 hour)
cleanOldFiles(UPLOAD_DIR);
cleanOldFiles(CLEANED_DIR);

function cleanOldFiles($dir) {
    $files = glob($dir . '*');
    $now = time();
    foreach ($files as $file) {
        if (is_file($file) && $now - filemtime($file) >= 3600) {
            unlink($file);
        }
    }
}

function validateImage($file) {
    if (!in_array($file['type'], ALLOWED_TYPES)) {
        return ['valid' => false, 'error' => 'Type de fichier non supporté'];
    }
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['valid' => false, 'error' => 'Fichier trop volumineux'];
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['valid' => false, 'error' => 'Erreur lors de l\'upload'];
    }
    return ['valid' => true];
}

/**
 * Clean image metadata without re-encoding
 * Uses binary manipulation to preserve image quality
 */
function cleanImage($sourcePath, $destPath, $mimeType, $mode) {
    return ImageCleaner::clean($sourcePath, $destPath, $mimeType, $mode);
}

// Main processing
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

if (!isset($_FILES['images']) || !isset($_POST['mode'])) {
    echo json_encode(['success' => false, 'message' => 'Données manquantes']);
    exit;
}

$mode = $_POST['mode'];
$files = $_FILES['images'];
$processedImages = [];

// Handle multiple files
$fileCount = count($files['name']);

for ($i = 0; $i < $fileCount; $i++) {
    $file = [
        'name' => $files['name'][$i],
        'type' => $files['type'][$i],
        'tmp_name' => $files['tmp_name'][$i],
        'error' => $files['error'][$i],
        'size' => $files['size'][$i]
    ];

    // Validate
    $validation = validateImage($file);
    if (!$validation['valid']) {
        continue;
    }

    // Generate unique filename
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('img_', true) . '.' . $extension;
    $uploadPath = UPLOAD_DIR . $filename;
    $cleanedFilename = 'clean_' . $filename;
    $cleanedPath = CLEANED_DIR . $cleanedFilename;

    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
        continue;
    }

    // Clean image using binary manipulation (preserves quality)
    $success = cleanImage($uploadPath, $cleanedPath, $file['type'], $mode);

    if ($success) {
        $processedImages[] = [
            'filename' => $cleanedFilename,
            'path' => 'cleaned/' . $cleanedFilename,
            'original' => $file['name']
        ];
    }

    // Clean up original upload
    unlink($uploadPath);
}

if (empty($processedImages)) {
    echo json_encode(['success' => false, 'message' => 'Aucune image traitée']);
} else {
    echo json_encode(['success' => true, 'images' => $processedImages]);
}
