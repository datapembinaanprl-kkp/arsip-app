<script>
// ── Tab switching (index page) ─────────────────────────────────
document.querySelectorAll('.os-tab').forEach(tab => {
    tab.addEventListener('click', () => {
        // Deactivate all tabs and content panels
        document.querySelectorAll('.os-tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.os-tab-content').forEach(c => c.classList.remove('active'));

        // Activate clicked tab and its content panel
        tab.classList.add('active');
        document.getElementById(tab.dataset.target)?.classList.add('active');
    });
});
</script>