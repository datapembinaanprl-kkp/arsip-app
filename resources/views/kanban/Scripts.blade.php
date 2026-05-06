<script>
// ── State modal ────────────────────────────────────────────────
let activeCard       = null;
let selectedStatus   = null;

const STATUS_LABELS = @json(collect(\App\Models\Document::STATUSES)->map(fn($s) => $s['label']));

// ── Buka modal saat klik "Pindah Status" ──────────────────────
function openModal(btn) {
    activeCard = btn.closest('.kb-card');

    const name        = activeCard.dataset.name;
    const transitions = JSON.parse(activeCard.dataset.transitions || '[]');

    selectedStatus = null;

    document.getElementById('modal-doc-name').textContent = name;
    document.getElementById('modal-catatan').value = '';

    // Render pill options
    const container = document.getElementById('modal-status-options');
    container.innerHTML = '';

    transitions.forEach(status => {
        const pill = document.createElement('button');
        pill.className  = `kb-status-option kb-pill-${status}`;
        pill.textContent = STATUS_LABELS[status] ?? status;
        pill.dataset.status = status;
        pill.addEventListener('click', () => selectStatus(status, pill));
        container.appendChild(pill);
    });

    // Tampilkan modal
    document.getElementById('status-modal').style.display = 'flex';
}

// ── Pilih status dari pill ─────────────────────────────────────
function selectStatus(status, pill) {
    selectedStatus = status;

    // Reset semua pill
    document.querySelectorAll('.kb-status-option').forEach(p => p.classList.remove('selected'));
    pill.classList.add('selected');

    // Wajibkan catatan saat revisi
    const isRevisi = status === 'revisi';
    document.getElementById('catatan-required').style.display = isRevisi ? 'inline' : 'none';
    document.getElementById('modal-catatan').placeholder = isRevisi
        ? 'Tuliskan alasan pengembalian dokumen... (wajib)'
        : 'Tuliskan catatan (opsional)...';
}

// ── Tutup modal ────────────────────────────────────────────────
function closeModal() {
    document.getElementById('status-modal').style.display = 'none';
    activeCard     = null;
    selectedStatus = null;
}

// ── Submit perubahan status via AJAX ──────────────────────────
async function submitStatusChange() {
    if (!selectedStatus) {
        alert('Pilih status tujuan terlebih dahulu.');
        return;
    }

    const catatan = document.getElementById('modal-catatan').value.trim();

    // Validasi: catatan wajib untuk revisi
    if (selectedStatus === 'revisi' && !catatan) {
        alert('Alasan pengembalian wajib diisi.');
        document.getElementById('modal-catatan').focus();
        return;
    }

    const url = activeCard.dataset.updateUrl;

    const btn = document.getElementById('modal-submit');
    btn.disabled     = true;
    btn.textContent  = 'Menyimpan...';

    try {
        const res = await fetch(url, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept':       'application/json',
            },
            body: JSON.stringify({ status: selectedStatus, catatan }),
        });

        const data = await res.json();

        if (!res.ok) {
            alert(data.message || 'Terjadi kesalahan.');
            return;
        }

        // Reload halaman agar board ter-refresh
        window.location.reload();

    } catch (e) {
        alert('Gagal terhubung ke server.');
    } finally {
        btn.disabled    = false;
        btn.textContent = 'Simpan Perubahan';
    }
}

// ── Tutup modal saat klik overlay ─────────────────────────────
document.getElementById('status-modal').addEventListener('click', function (e) {
    if (e.target === this) closeModal();
});

// ── Tutup modal dengan Escape ──────────────────────────────────
document.addEventListener('keydown', e => {
    if (e.key === 'Escape') closeModal();
});
</script>