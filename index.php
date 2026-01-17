<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ExifRemover - Nettoyage d'images par lot</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <header>
            <h1>
                <svg class="logo-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M21 15V19C21 19.5304 20.7893 20.0391 20.4142 20.4142C20.0391 20.7893 19.5304 21 19 21H5C4.46957 21 3.96086 20.7893 3.58579 20.4142C3.21071 20.0391 3 19.5304 3 19V15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M7 10L12 15L17 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M12 15V3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                ExifRemover
            </h1>
            <p class="subtitle">Nettoyez vos images en toute simplicité</p>
        </header>

        <div class="upload-section">
            <div class="mode-selector">
                <label class="mode-option">
                    <input type="radio" name="cleanMode" value="ai" checked>
                    <div class="mode-card">
                        <div class="mode-icon">🤖</div>
                        <h3>Nettoyage AI</h3>
                        <p>Supprime uniquement les tags AI (C2PA, paramètres de génération) tout en préservant les EXIF caméra, GPS et copyright</p>
                    </div>
                </label>
                <label class="mode-option">
                    <input type="radio" name="cleanMode" value="full">
                    <div class="mode-card">
                        <div class="mode-icon">🧹</div>
                        <h3>Nettoyage Complet</h3>
                        <p>Supprime TOUTES les métadonnées : EXIF, GPS, timestamps, réglages caméra et copyright. Fichier totalement nettoyé</p>
                    </div>
                </label>
            </div>

            <div class="drop-zone" id="dropZone">
                <svg class="upload-icon" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M21 15V19C21 19.5304 20.7893 20.0391 20.4142 20.4142C20.0391 20.7893 19.5304 21 19 21H5C4.46957 21 3.96086 20.7893 3.58579 20.4142C3.21071 20.0391 3 19.5304 3 19V15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M17 8L12 3L7 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M12 3V15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <h2>Glissez vos images ici</h2>
                <p>ou cliquez pour sélectionner</p>
                <p class="file-info">Formats acceptés : JPG, PNG, WEBP • Jusqu'à 50 images</p>
                <input type="file" id="fileInput" multiple accept="image/jpeg,image/png,image/webp" style="display: none;">
            </div>

            <div class="preview-section" id="previewSection" style="display: none;">
                <h3>Images sélectionnées (<span id="fileCount">0</span>)</h3>
                <div class="preview-grid" id="previewGrid"></div>
                <button class="btn-primary" id="processBtn">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 12L11 14L15 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Nettoyer les images
                </button>
            </div>
        </div>

        <div class="results-section" id="resultsSection" style="display: none;">
            <h2>Images nettoyées ✨</h2>
            <div class="gallery-grid" id="galleryGrid"></div>
            <div class="download-all-container">
                <button class="btn-download-all" id="downloadAllBtn">
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M21 15V19C21 19.5304 20.7893 20.0391 20.4142 20.4142C20.0391 20.7893 19.5304 21 19 21H5C4.46957 21 3.96086 20.7893 3.58579 20.4142C3.21071 20.0391 3 19.5304 3 19V15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M7 10L12 15L17 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M12 15V3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Télécharger toutes les images
                </button>
            </div>
        </div>

        <div class="loading-overlay" id="loadingOverlay" style="display: none;">
            <div class="loading-spinner">
                <div class="spinner"></div>
                <p>Nettoyage en cours...</p>
                <p class="loading-progress" id="loadingProgress">0%</p>
            </div>
        </div>
    </div>

    <script src="assets/js/app.js"></script>
</body>
</html>
