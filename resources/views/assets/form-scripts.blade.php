<script>
// ── File input preview & label update ─────────────────────────
function bindFileInput(inputId, nameId, previewId) {
    const input   = document.getElementById(inputId);
    const nameEl  = document.getElementById(nameId);
    const preview = document.getElementById(previewId);

    input?.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;

        if (nameEl) nameEl.textContent = file.name;

        // Show image preview only for image inputs
        if (preview && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = e => {
                preview.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    });
}

bindFileInput('foto',    'foto-name',    'foto-preview');
bindFileInput('dokumen', 'dokumen-name', null);
</script>