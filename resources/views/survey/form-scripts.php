<script>
// ── State ──────────────────────────────────────────────────────
let questionCount = document.querySelectorAll('.sv-qblock').length;

// ── Tambah pertanyaan baru ─────────────────────────────────────
function addQuestion() {
    const idx       = questionCount++;
    const container = document.getElementById('questions-container');

    const block = document.createElement('div');
    block.className    = 'sv-question-block';
    block.dataset.index = idx;

    block.innerHTML = buildQuestionHtml(idx);
    container.appendChild(block);

    // Scroll ke pertanyaan baru
    block.scrollIntoView({ behavior: 'smooth', block: 'center' });

    // Update nomor semua pertanyaan
    reorderNumbers();
}

// ── Hapus pertanyaan ──────────────────────────────────────────
function removeQuestion(idx) {
    const block = document.getElementById('qblock-' + idx);
    if (!block) return;

    // Minimal harus ada 1 pertanyaan
    if (document.querySelectorAll('.sv-qblock').length <= 1) {
        alert('Survey harus punya minimal satu pertanyaan.');
        return;
    }

    block.closest('.sv-question-block').remove();
    reorderNumbers();
}

// ── Handler saat tipe berubah ─────────────────────────────────
function onTypeChange(idx, type) {
    const optionsField   = document.getElementById('options-field-' + idx);
    const ratingPreview  = document.getElementById('rating-preview-' + idx);
    const typeLabel      = document.getElementById('type-label-' + idx);

    const needsOptions = ['radio', 'checkbox', 'select'].includes(type);

    optionsField.style.display  = needsOptions ? '' : 'none';
    ratingPreview.style.display = type === 'rating' ? '' : 'none';

    // Update label header
    const labels = {
        text:     '📝 Teks Pendek',
        textarea: '📄 Teks Panjang',
        radio:    '🔘 Pilihan Ganda',
        checkbox: '☑️ Checkbox',
        select:   '📋 Dropdown',
        date:     '📅 Tanggal',
        rating:   '⭐ Rating (1–5)',
    };
    if (typeLabel) typeLabel.textContent = labels[type] ?? type;
}

// ── Build HTML satu blok pertanyaan baru ──────────────────────
function buildQuestionHtml(i) {
    return `
    <div class="sv-qblock" id="qblock-${i}">
        <div class="sv-qblock-header">
            <span class="sv-qblock-num">${i + 1}</span>
            <span class="sv-qblock-type-label" id="type-label-${i}">📝 Teks Pendek</span>
            <button type="button" class="sv-qblock-remove"
                    onclick="removeQuestion(${i})" title="Hapus">✕</button>
        </div>
        <div class="sv-qblock-body">
            <div class="sv-field">
                <label class="sv-label">Pertanyaan <span class="sv-req">*</span></label>
                <input type="text" name="questions[${i}][label]"
                       class="sv-input" placeholder="Tulis pertanyaan..." required>
            </div>
            <div class="sv-field">
                <label class="sv-label">Tipe Jawaban</label>
                <select name="questions[${i}][type]"
                        class="sv-input sv-type-select"
                        onchange="onTypeChange(${i}, this.value)">
                    <option value="text">📝 Teks Pendek</option>
                    <option value="textarea">📄 Teks Panjang</option>
                    <option value="radio">🔘 Pilihan Ganda</option>
                    <option value="checkbox">☑️ Checkbox</option>
                    <option value="select">📋 Dropdown</option>
                    <option value="date">📅 Tanggal</option>
                    <option value="rating">⭐ Rating (1–5)</option>
                </select>
            </div>
            <div class="sv-field sv-options-field" id="options-field-${i}" style="display:none">
                <label class="sv-label">
                    Opsi Jawaban <span class="sv-req">*</span>
                    <span class="sv-hint" style="font-weight:400"> — satu opsi per baris</span>
                </label>
                <textarea name="questions[${i}][options]"
                          class="sv-input sv-textarea" rows="4"
                          placeholder="Opsi 1&#10;Opsi 2&#10;Opsi 3"></textarea>
            </div>
            <div id="rating-preview-${i}" style="display:none;margin-top:.5rem">
                <div class="sv-rating-preview">
                    <span class="sv-star">★</span><span class="sv-star">★</span>
                    <span class="sv-star">★</span><span class="sv-star">★</span>
                    <span class="sv-star">★</span>
                    <span class="sv-hint" style="margin-left:.5rem">1 – 5</span>
                </div>
            </div>
            <div style="margin-top:.75rem">
                <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;font-size:.8125rem">
                    <input type="checkbox" name="questions[${i}][required]" value="1"
                           style="accent-color:var(--blue);width:15px;height:15px">
                    <span style="color:var(--text-2)">Wajib dijawab</span>
                </label>
            </div>
        </div>
    </div>`;
}

// ── Update nomor urut setelah hapus ───────────────────────────
function reorderNumbers() {
    document.querySelectorAll('.sv-qblock').forEach((block, idx) => {
        const numEl = block.querySelector('.sv-qblock-num');
        if (numEl) numEl.textContent = idx + 1;
    });
}

// ── Copy URL publik ────────────────────────────────────────────
function copyUrl() {
    const input = document.getElementById('publik-url');
    if (!input) return;
    navigator.clipboard.writeText(input.value);
    const btn = document.getElementById('copy-btn');
    btn.textContent = '✓ Disalin!';
    setTimeout(() => btn.textContent = '📋 Salin', 2000);
}
</script>