function getFileName(path) {
    return path.split('/').pop();
}



function isValidAudioSrc(url) {
    return url && url.startsWith('/storage/') && (url.endsWith('.mp3') || url.endsWith('.wav'));
}


function toggleHoverPlay(button, src) {
    const hoverAudio = document.getElementById('hover-audio-player');
    let playIcon = button.querySelector('.play-icon');
    let pauseIcon = button.querySelector('.pause-icon');

    if (!hoverAudio || !playIcon || !pauseIcon) return;

    const isSame = hoverAudio.src === new URL(src, window.location.origin).href;

    if (isSame) {
        if (hoverAudio.paused) {
            hoverAudio.play();
            playIcon.classList.add('hidden');
            pauseIcon.classList.remove('hidden');
        } else {
            hoverAudio.pause();
            playIcon.classList.remove('hidden');
            pauseIcon.classList.add('hidden');
        }
    } else {
        if (!isValidAudioSrc(src)) {
            console.warn('Blocked invalid audio path:', src);
            return;
        }

        hoverAudio.pause();
        hoverAudio.src = src;
        hoverAudio.play();

        if (window.currentHoverButton) {
            window.currentHoverButton.querySelector('.play-icon')?.classList.remove('hidden');
            window.currentHoverButton.querySelector('.pause-icon')?.classList.add('hidden');
        }

        playIcon.classList.add('hidden');
        pauseIcon.classList.remove('hidden');
        window.currentHoverButton = button;
    }

    hoverAudio.onended = () => {
        playIcon.classList.remove('hidden');
        pauseIcon.classList.add('hidden');
    };
}

// make them accessible globally:
window.toggleHoverPlay = toggleHoverPlay;
window.getFileName = getFileName;
