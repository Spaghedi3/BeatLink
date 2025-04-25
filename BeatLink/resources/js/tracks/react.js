document.querySelectorAll('.reaction-button').forEach(button => {
    button.addEventListener('click', async () => {
        const trackId = button.dataset.trackId;
        const ownerId = button.dataset.ownerId;
        const reaction = button.dataset.reaction;
        const opposite = reaction === 'love' ? 'hate' : 'love';

        const loveCountSpan = document.querySelector(`#count-love-${trackId}`);
        const hateCountSpan = document.querySelector(`#count-hate-${trackId}`);

        try {
            const response = await fetch(window.routes.react, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ track_id: trackId, owner_id: ownerId, reaction }),
            });

            const data = await response.json();

            const allButtons = document.querySelectorAll(`.reaction-button[data-track-id="${trackId}"]`);
            allButtons.forEach(btn => btn.classList.remove('text-red-500'));

            if (data.status === 'reacted') {
                button.classList.add('text-red-500');
                if (reaction === 'love') loveCountSpan.textContent++;
                else hateCountSpan.textContent++;
            }

            if (data.status === 'switched') {
                button.classList.add('text-red-500');
                if (reaction === 'love') {
                    loveCountSpan.textContent++;
                    hateCountSpan.textContent--;
                } else {
                    hateCountSpan.textContent++;
                    loveCountSpan.textContent--;
                }
            }

            if (data.status === 'removed') {
                if (reaction === 'love') loveCountSpan.textContent--;
                else hateCountSpan.textContent--;
            }
        } catch (err) {
            console.error('Error reacting:', err);
        }
    });
});
