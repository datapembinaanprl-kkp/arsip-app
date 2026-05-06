{{--
    Partial satu blok pertanyaan di form builder.
    $i = index (integer)
    $q = SurveyQuestion|null
--}}
@php
    $types = [
        'text'     => ['label' => 'Teks Pendek',    'icon' => '📝'],
        'textarea' => ['label' => 'Teks Panjang',   'icon' => '📄'],
        'radio'    => ['label' => 'Pilihan Ganda',  'icon' => '🔘'],
        'checkbox' => ['label' => 'Checkbox',        'icon' => '☑️'],
        'select'   => ['label' => 'Dropdown',        'icon' => '📋'],
        'date'     => ['label' => 'Tanggal',         'icon' => '📅'],
        'rating'   => ['label' => 'Rating (1–5)',    'icon' => '⭐'],
    ];
    $currentType    = old("questions.{$i}.type", $q?->type ?? 'text');
    $currentLabel   = old("questions.{$i}.label", $q?->label ?? '');
    $currentOptions = old("questions.{$i}.options",
        $q?->options ? implode("\n", $q->options) : '');
    $currentReq     = old("questions.{$i}.required", $q?->required ?? false);
    $needsOptions   = in_array($currentType, ['radio', 'checkbox', 'select']);
@endphp

<div class="sv-qblock" id="qblock-{{ $i }}">

    {{-- Header blok --}}
    <div class="sv-qblock-header">
        <span class="sv-qblock-num">{{ $i + 1 }}</span>
        <span class="sv-qblock-type-label" id="type-label-{{ $i }}">
            {{ $types[$currentType]['icon'] }} {{ $types[$currentType]['label'] }}
        </span>
        <button type="button" class="sv-qblock-remove"
                onclick="removeQuestion({{ $i }})" title="Hapus pertanyaan">✕</button>
    </div>

    {{-- Body blok --}}
    <div class="sv-qblock-body">

        {{-- Label pertanyaan --}}
        <div class="sv-field">
            <label class="sv-label">Pertanyaan <span class="sv-req">*</span></label>
            <input type="text" name="questions[{{ $i }}][label]"
                   class="sv-input" value="{{ $currentLabel }}"
                   placeholder="Tulis pertanyaan di sini..." required>
        </div>

        {{-- Tipe pertanyaan --}}
        <div class="sv-field">
            <label class="sv-label">Tipe Jawaban</label>
            <select name="questions[{{ $i }}][type]"
                    class="sv-input sv-type-select"
                    data-index="{{ $i }}"
                    onchange="onTypeChange({{ $i }}, this.value)">
                @foreach($types as $val => $cfg)
                    <option value="{{ $val }}" {{ $currentType === $val ? 'selected' : '' }}>
                        {{ $cfg['icon'] }} {{ $cfg['label'] }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Opsi (untuk radio/checkbox/select) --}}
        <div class="sv-field sv-options-field" id="options-field-{{ $i }}"
             style="{{ $needsOptions ? '' : 'display:none' }}">
            <label class="sv-label">
                Opsi Jawaban <span class="sv-req">*</span>
                <span class="sv-hint" style="font-weight:400"> — satu opsi per baris</span>
            </label>
            <textarea name="questions[{{ $i }}][options]"
                      class="sv-input sv-textarea"
                      rows="4"
                      placeholder="Opsi 1&#10;Opsi 2&#10;Opsi 3">{{ $currentOptions }}</textarea>
        </div>

        {{-- Preview rating --}}
        <div id="rating-preview-{{ $i }}"
             style="{{ $currentType === 'rating' ? '' : 'display:none' }};margin-top:.5rem">
            <div class="sv-rating-preview">
                @for($s = 1; $s <= 5; $s++)
                    <span class="sv-star">★</span>
                @endfor
                <span class="sv-hint" style="margin-left:.5rem">1 – 5</span>
            </div>
        </div>

        {{-- Wajib diisi --}}
        <div style="margin-top:.75rem">
            <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer;font-size:.8125rem">
                <input type="checkbox" name="questions[{{ $i }}][required]"
                       value="1"
                       style="accent-color:var(--blue);width:15px;height:15px"
                       {{ $currentReq ? 'checked' : '' }}>
                <span style="color:var(--text-2)">Wajib dijawab</span>
            </label>
        </div>

    </div>
</div>