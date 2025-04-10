function togglePlay(beatId) {
    console.log("togglePlay called for beat:", beatId);

    const audio = document.getElementById(`audio-${beatId}`);
    const playIcon = document.getElementById(`playIcon-${beatId}`);
    const pauseIcon = document.getElementById(`pauseIcon-${beatId}`);

    if (!audio) {
        console.error("Audio element not found for beat:", beatId);
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