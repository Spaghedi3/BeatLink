let audioElements = [];

export function registerAudio(audio) {
    audioElements.push(audio);
}

export function destroyAudio() {
    audioElements.forEach(audio => {
        audio.pause();
        audio.src = '';
        audio.load();
    });
    audioElements = [];

    document.querySelectorAll('audio').forEach(audio => {
        audio.pause();
        audio.src = '';
        audio.load();
    });
}
