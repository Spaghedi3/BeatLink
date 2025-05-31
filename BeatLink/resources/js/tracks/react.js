// resources/js/tracks/react.js

//pull csrf tokens
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
const reactRoute = document.querySelector('meta[name="react-route"]')?.content || '';

// keep a counter of the last request we sent for each track
const lastRequestId = {};

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.reaction-button').forEach(button => {
        button.addEventListener('click', async () => {
            const trackId = button.dataset.trackId;
            const ownerId = button.dataset.ownerId;
            const reaction = button.dataset.reaction;
            const other = reaction === 'love' ? 'hate' : 'love';

            const btnThis = button;
            const btnOther = document.querySelector(
                `.reaction-button[data-track-id="${trackId}"][data-reaction="${other}"]`
            );
            const spanThis = document.getElementById(`count-${reaction}-${trackId}`);
            const spanOther = document.getElementById(`count-${other}-${trackId}`);

            const thisActive = btnThis.classList.contains('text-red-500');
            const otherActive = btnOther?.classList.contains('text-red-500');

            if (reaction === 'love') {
                if (thisActive) {
                    //love→love: remove love
                    btnThis.classList.remove('text-red-500');
                    spanThis.textContent = Math.max(0, +spanThis.textContent - 1);
                } else if (otherActive) {
                    //hate→love
                    btnOther.classList.remove('text-red-500');
                    spanOther.textContent = Math.max(0, +spanOther.textContent - 1);
                    btnThis.classList.add('text-red-500');
                    spanThis.textContent = +spanThis.textContent + 1;
                } else {
                    //none→love
                    btnThis.classList.add('text-red-500');
                    spanThis.textContent = +spanThis.textContent + 1;
                }
            } else {
                if (thisActive) {
                    //hate→hate: remove hate
                    btnThis.classList.remove('text-red-500');
                    spanThis.textContent = Math.max(0, +spanThis.textContent - 1);
                } else if (otherActive) {
                    //love→hate
                    btnOther.classList.remove('text-red-500');
                    spanOther.textContent = Math.max(0, +spanOther.textContent - 1);
                    btnThis.classList.add('text-red-500');
                    spanThis.textContent = +spanThis.textContent + 1;
                } else {
                    //none→hate
                    btnThis.classList.add('text-red-500');
                    spanThis.textContent = +spanThis.textContent + 1;
                }
            }
            const reqId = (lastRequestId[trackId] || 0) + 1;
            lastRequestId[trackId] = reqId;

            try {
                const res = await fetch(window.routes.react, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document
                            .querySelector('meta[name="csrf-token"]')
                            .content
                    },
                    body: JSON.stringify({ track_id: trackId, owner_id: ownerId, reaction })
                });
                const data = await res.json();

                if (lastRequestId[trackId] !== reqId) return;
                document
                    .querySelectorAll(`.reaction-button[data-track-id="${trackId}"]`)
                    .forEach(b => b.classList.remove('text-red-500'));
                if (data.status !== 'removed') {
                    document
                        .querySelector(
                            `.reaction-button[data-track-id="${trackId}"][data-reaction="${data.reaction}"]`
                        )
                        .classList.add('text-red-500');
                }
                document.getElementById(`count-love-${trackId}`).textContent = data.love_count;
                document.getElementById(`count-hate-${trackId}`).textContent = data.hate_count;

            } catch (err) {
                console.error('Reaction error:', err);
            }
        });
    });
});
