<script>
// ── Photo file input: show filename + live preview ─────────────
const photoInput   = document.getElementById('photo');
const fileNameSpan = document.getElementById('file-name');
const photoPreview = document.getElementById('photo-preview');

photoInput?.addEventListener('change', function () {
    const file = this.files[0];

    if (!file) {
        // Ubah teks fallback file input
    fileNameSpan.textContent = 'Belum ada file dipilih';
        return;
    }

    fileNameSpan.textContent = file.name;

    // Show image preview before upload
    const reader = new FileReader();
    reader.onload = e => {
        photoPreview.src = e.target.result;
        photoPreview.style.display = 'block';
    };
    reader.readAsDataURL(file);
});
</script>