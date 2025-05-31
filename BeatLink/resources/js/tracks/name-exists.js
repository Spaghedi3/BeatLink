document.addEventListener('DOMContentLoaded', () => {
    const nameInput = document.getElementById('name');
    const warning = document.getElementById('name-warning');
    const submitBtn = document.getElementById('submitBtn');
    const updateBtn = document.getElementById('updateBtn');
    if (!nameInput || !warning) return;   // nothing to do

    const originalName = document.getElementById('original_name')?.value || '';
    const exceptId = nameInput.dataset.exceptId || '';

    nameInput.addEventListener('input', function () {
        const val = this.value.trim();
        // reset if empty or unchanged
        if (!val || val === originalName) {
            warning.textContent = '';
            submitBtn?.removeAttribute('disabled');
            updateBtn?.removeAttribute('disabled');
            return;
        }

        fetch(`/tracks/check-name?name=${encodeURIComponent(val)}&except_id=${exceptId}`)
            .then(r => r.json())
            .then(data => {
                if (data.exists) {
                    warning.textContent = 'You already have an entry with this name.';
                    submitBtn?.setAttribute('disabled', '');
                    updateBtn?.setAttribute('disabled', '');
                } else {
                    warning.textContent = '';
                    submitBtn?.removeAttribute('disabled');
                    updateBtn?.removeAttribute('disabled');
                }
            })
            .catch(console.error);
    });
});