function togglePlay(trackId) {
    console.log("togglePlay called for track:", trackId);

    const audio = document.getElementById(`audio-${trackId}`);
    const playIcon = document.getElementById(`playIcon-${trackId}`);
    const pauseIcon = document.getElementById(`pauseIcon-${trackId}`);

    if (!audio) {
        console.error("Audio element not found for track:", trackId);
        return;
    }

    if (audio.paused) {
        audio.play();
        playIcon.classList.add('hidden');
        pauseIcon.classList.remove('hidden');
    } else {
        audio.pause();
        playIcon.classList.remove('hidden');
        pauseIcon.classList.add('hidden');
    }
}

window.togglePlay = togglePlay;