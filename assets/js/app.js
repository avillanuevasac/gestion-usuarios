document.addEventListener('DOMContentLoaded', () => {
    // Toggle password visibility
    document.querySelectorAll('#togglePass').forEach(btn => {
        btn.addEventListener('click', () => {
            const input = document.getElementById('password');
            const icon  = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'bi bi-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'bi bi-eye';
            }
        });
    });

    // Password strength meter
    const passInput = document.getElementById('password');
    const bar       = document.getElementById('strengthBar');
    if (passInput && bar) {
        passInput.addEventListener('input', () => {
            const v = passInput.value;
            bar.className = 'password-strength';
            if (v.length === 0) return;
            if (v.length < 6) bar.classList.add('strength-weak');
            else if (v.length < 10 || !/[A-Z]/.test(v) || !/[0-9]/.test(v)) bar.classList.add('strength-medium');
            else bar.classList.add('strength-strong');
        });
    }

    // Password match checker
    const confirmInput = document.getElementById('confirm');
    const matchMsg     = document.getElementById('matchMsg');
    if (confirmInput && matchMsg && passInput) {
        confirmInput.addEventListener('input', () => {
            if (confirmInput.value === passInput.value) {
                matchMsg.textContent = '✓ Las contraseñas coinciden';
                matchMsg.className   = 'form-text text-success';
            } else {
                matchMsg.textContent = '✗ Las contraseñas no coinciden';
                matchMsg.className   = 'form-text text-danger';
            }
        });
    }

    // Avatar preview
    const avatarInput = document.getElementById('avatarInput');
    if (avatarInput) {
        avatarInput.addEventListener('change', function () {
            const file = this.files[0];
            if (!file) return;
            const reader = new FileReader();
            reader.onload = e => {
                const preview = document.getElementById('avatarPreview');
                const placeholder = document.getElementById('avatarPlaceholder');
                preview.src = e.target.result;
                preview.classList.remove('d-none');
                if (placeholder) placeholder.classList.add('d-none');
            };
            reader.readAsDataURL(file);
        });
    }
});

// Delete confirmation modal
function confirmDelete(id, name, baseUrl) {
    document.getElementById('deleteUserName').textContent = name;
    document.getElementById('deleteLink').href = baseUrl + '?action=delete&id=' + id;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
