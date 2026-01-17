let selectedFiles = [];

// Elements
const dropZone = document.getElementById('dropZone');
const fileInput = document.getElementById('fileInput');
const previewSection = document.getElementById('previewSection');
const previewGrid = document.getElementById('previewGrid');
const fileCount = document.getElementById('fileCount');
const processBtn = document.getElementById('processBtn');
const resultsSection = document.getElementById('resultsSection');
const galleryGrid = document.getElementById('galleryGrid');
const loadingOverlay = document.getElementById('loadingOverlay');
const loadingProgress = document.getElementById('loadingProgress');
const downloadAllBtn = document.getElementById('downloadAllBtn');

// Drag and drop handlers
dropZone.addEventListener('click', () => fileInput.click());

dropZone.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropZone.classList.add('drag-over');
});

dropZone.addEventListener('dragleave', () => {
    dropZone.classList.remove('drag-over');
});

dropZone.addEventListener('drop', (e) => {
    e.preventDefault();
    dropZone.classList.remove('drag-over');
    handleFiles(e.dataTransfer.files);
});

fileInput.addEventListener('change', (e) => {
    handleFiles(e.target.files);
});

function handleFiles(files) {
    const validFiles = Array.from(files).filter(file => {
        return file.type === 'image/jpeg' ||
               file.type === 'image/png' ||
               file.type === 'image/webp';
    });

    if (validFiles.length > 50) {
        alert('Maximum 50 images à la fois');
        return;
    }

    selectedFiles = validFiles;
    displayPreviews();
}

function displayPreviews() {
    previewGrid.innerHTML = '';
    fileCount.textContent = selectedFiles.length;

    selectedFiles.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = (e) => {
            const previewItem = document.createElement('div');
            previewItem.className = 'preview-item';
            previewItem.innerHTML = `
                <img src="${e.target.result}" alt="Preview">
                <button class="remove-btn" onclick="removeFile(${index})">×</button>
            `;
            previewGrid.appendChild(previewItem);
        };
        reader.readAsDataURL(file);
    });

    previewSection.style.display = selectedFiles.length > 0 ? 'block' : 'none';
}

function removeFile(index) {
    selectedFiles.splice(index, 1);
    displayPreviews();
}

// Process images
processBtn.addEventListener('click', async () => {
    if (selectedFiles.length === 0) return;

    const cleanMode = document.querySelector('input[name="cleanMode"]:checked').value;

    loadingOverlay.style.display = 'flex';
    resultsSection.style.display = 'none';

    const formData = new FormData();
    selectedFiles.forEach((file, index) => {
        formData.append('images[]', file);
    });
    formData.append('mode', cleanMode);

    try {
        const response = await fetch('upload.php', {
            method: 'POST',
            body: formData
        });

        const result = await response.json();

        if (result.success) {
            displayResults(result.images);
        } else {
            alert('Erreur lors du traitement : ' + result.message);
        }
    } catch (error) {
        alert('Erreur : ' + error.message);
    } finally {
        loadingOverlay.style.display = 'none';
    }
});

function displayResults(images) {
    galleryGrid.innerHTML = '';

    images.forEach((image) => {
        const galleryItem = document.createElement('div');
        galleryItem.className = 'gallery-item';
        galleryItem.innerHTML = `
            <img src="${image.path}" alt="Cleaned image">
            <button class="download-btn" onclick="downloadImage('${image.path}', '${image.filename}')">
                <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M21 15V19C21 19.5304 20.7893 20.0391 20.4142 20.4142C20.0391 20.7893 19.5304 21 19 21H5C4.46957 21 3.96086 20.7893 3.58579 20.4142C3.21071 20.0391 3 19.5304 3 19V15" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M7 10L12 15L17 10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M12 15V3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Download
            </button>
        `;
        galleryGrid.appendChild(galleryItem);
    });

    resultsSection.style.display = 'block';
    resultsSection.scrollIntoView({ behavior: 'smooth' });
}

function downloadImage(path, filename) {
    const link = document.createElement('a');
    link.href = path;
    link.download = filename;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

downloadAllBtn.addEventListener('click', async () => {
    const images = galleryGrid.querySelectorAll('.gallery-item img');

    for (let i = 0; i < images.length; i++) {
        const img = images[i];
        const filename = `cleaned_${i + 1}.jpg`;
        await new Promise(resolve => {
            setTimeout(() => {
                downloadImage(img.src, filename);
                resolve();
            }, 300 * i);
        });
    }
});
