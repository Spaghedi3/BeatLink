import { destroyAudio, registerAudio } from '../cleanup.js';

const playbackStartTimes = {};           // trackId → timestamp
const MIN_LISTEN_DURATION = 3;           // Minimum seconds to count as a listen

// Read user ID and CSRF token from meta tags:
const metaUser = document.querySelector('meta[name="user-id"]');
const metaCsrf = document.querySelector('meta[name="csrf-token"]');
const userId = metaUser ? metaUser.content : null;
const csrfToken = metaCsrf ? metaCsrf.content : '';

window.Laravel = {
    userId,
    csrfToken
};

function togglePlay(trackId) {
    try {
        const audio = document.getElementById(`audio-${trackId}`);
        const playIcon = document.getElementById(`playIcon-${trackId}`);
        const pauseIcon = document.getElementById(`pauseIcon-${trackId}`);

        if (!audio) {
            console.error(`Audio element not found: audio-${trackId}`);
            return;
        }

        registerAudio(audio);

        if (audio.paused) {
            // Pause any other playing tracks first
            document.querySelectorAll('audio').forEach(other => {
                if (other !== audio && !other.paused) {
                    other.pause();
                    const otherId = other.id.replace('audio-', '');
                    handlePlaybackStop(otherId);
                }
            });

            audio.play()
                .then(() => {
                    playbackStartTimes[trackId] = Date.now();
                    playIcon?.classList.add('hidden');
                    pauseIcon?.classList.remove('hidden');
                })
                .catch(err => console.error('Playback error:', err));

        } else {
            audio.pause();
            handlePlaybackStop(trackId, playIcon, pauseIcon);
        }

    } catch (err) {
        console.error('togglePlay error:', err);
    }
}

function handlePlaybackStop(trackId, playIcon, pauseIcon) {
    playIcon?.classList.remove('hidden');
    pauseIcon?.classList.add('hidden');

    const start = playbackStartTimes[trackId];
    if (!start) return;

    const durationSec = Math.round((Date.now() - start) / 1000);
    if (durationSec >= MIN_LISTEN_DURATION) {
        sendListenInteraction(trackId, durationSec);
    }

    delete playbackStartTimes[trackId];
}

function sendListenInteraction(beatId, duration) {
    // skip if not logged in
    if (!window.Laravel.userId) {
        console.log('Guest — not sending interaction');
        return Promise.resolve();
    }

    return fetch('/interactions', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': window.Laravel.csrfToken
        },
        body: JSON.stringify({
            user_id: window.Laravel.userId,
            beat_id: beatId,
            listen_duration: duration
        })
    })
        .then(res => {
            if (!res.ok) throw new Error('Network response was not ok');
            return res.json();
        })
        .then(data => {
            console.log('Interaction saved:', data);
            return data;
        })
        .catch(err => {
            console.error('Error sending interaction:', err);
            throw err;
        });
}

window.togglePlay = togglePlay;
window.sendListenInteraction = sendListenInteraction;

// === FOLDER PLAYBACK: exactly the same listen‐tracking ===
const hoverPlayer = document.getElementById('hover-audio-player');
if (hoverPlayer) {
    // when the folder file actually starts
    hoverPlayer.addEventListener('play', () => {
        const id = hoverPlayer.dataset.trackId;
        playbackStartTimes[id] = Date.now();
    });

    // when they pause or end that playback
    const stopHandler = () => {
        const id = hoverPlayer.dataset.trackId;
        handlePlaybackStop(id);
    };
    hoverPlayer.addEventListener('pause', stopHandler);
    hoverPlayer.addEventListener('ended', stopHandler);
}
