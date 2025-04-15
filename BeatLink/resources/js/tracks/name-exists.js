const nameInput = document.getElementById('name');
const warning = document.getElementById('name-warning');
const submitBtn = document.getElementById('submitBtn');
const updateBtn = document.getElementById('updateBtn');
const originalName = document.getElementById('original_name')?.value;
if (nameInput) {
    nameInput.addEventListener('input', function () {
        const name = this.value.trim();
        if (!name || name === originalName) {
            warning.textContent = '';
            if (submitBtn) submitBtn.disabled = false;
            if (updateBtn) updateBtn.disabled = false;
            return;
        }

        const trackId = "{{ $track->id ?? '' }}"; // works only in edit.blade.php
        fetch(`/tracks/check-name?name=${encodeURIComponent(name)}&except_id=${trackId}`)
            .then(res => res.json())
            .then(data => {
                if (data.exists) {
                    warning.textContent = 'You already have an entry with this name.';
                    if (submitBtn) submitBtn.disabled = true;
                    if (updateBtn) updateBtn.disabled = true;
                } else {
                    warning.textContent = '';
                    if (submitBtn) submitBtn.disabled = false;
                    if (updateBtn) updateBtn.disabled = false;
                }
            });
    });
}