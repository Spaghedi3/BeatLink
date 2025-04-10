document.addEventListener('DOMContentLoaded', () => {
    const category = document.getElementById('category');
    const fileSection = document.getElementById('add-beat');
    const folderSection = document.getElementById('add-folder');
    const fileInput = document.getElementById('audio_file');
    const folderInput = document.getElementById('audio_folder');

    if (!category || !fileSection || !folderSection) return; // page doesn't need this script

    category.addEventListener('change', function () {
        if (this.value === 'instrumental') {
            fileSection.classList.remove('hidden');
            fileInput?.setAttribute('required', '');

            folderSection.classList.add('hidden');
            folderInput?.removeAttribute('required');
        } else {
            fileSection.classList.add('hidden');
            fileInput?.removeAttribute('required');

            folderSection.classList.remove('hidden');
            folderInput?.setAttribute('required', '');
        }
    });

    if (category.value === 'instrumental') {
        fileSection.classList.remove('hidden');
        folderSection.classList.add('hidden');
    } else {
        fileSection.classList.add('hidden');
        folderSection.classList.remove('hidden');
    }
});
