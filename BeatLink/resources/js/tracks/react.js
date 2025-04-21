document.querySelectorAll('.reaction-button').forEach(button => {
    button.addEventListener('click', async () => {
        const trackId = button.dataset.trackId;
        const ownerId = button.dataset.ownerId;
        const reaction = button.dataset.reaction;

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

            // Reset both buttons for this track
            const allButtons = document.querySelectorAll(
                `.reaction-button[data-track-id="${trackId}"]`
            );
            allButtons.forEach(btn => btn.classList.remove('text-red-500'));

            // If it's still active, apply class
            if (data.status === 'reacted' || data.status === 'switched') {
                button.classList.add('text-red-500');
            }
        } catch (error) {
            console.error('Reaction error:', error);
        }
    });
});
