{{--
    Partial: Dynamic Form Fields (from FormPenilaian)
    Usage:
        @include('reviewer.partials.dynamic_fields', [
            'dynamicForm'    => $dynamicForm,      // FormPenilaian model or null
            'existingAnswers'=> $existingAnswers,   // array|null  (from extra_fields column)
            'inputPrefix'    => 'extra_fields',     // name prefix for inputs
        ])
--}}
@if($dynamicForm && !empty($dynamicForm->fields))
<div class="card card-custom mt-0 mb-3" id="dynamicFieldsCard">
    <div class="card-header card-header-custom d-flex align-items-center gap-2">
        <i class="fas fa-layer-group"></i>
        <div>
            <span class="fw-bold">{{ $dynamicForm->nama_form }}</span>
            <span style="font-size:0.72rem;opacity:0.8;margin-left:6px;">Form tambahan dari operator</span>
        </div>
    </div>
    <div class="card-body">
        @foreach($dynamicForm->fields as $idx => $field)
            @php
                $fieldKey   = 'field_' . $idx;
                $inputName  = ($inputPrefix ?? 'extra_fields') . '[' . $fieldKey . ']';
                $inputId    = ($inputPrefix ?? 'extra_fields') . '_' . $idx;
                $savedVal   = $existingAnswers[$fieldKey] ?? '';
                $isRequired = !empty($field['required']);
                $fieldType  = $field['type'] ?? 'textarea';
            @endphp

            <div class="mb-4 dynamic-field-row" data-type="{{ $fieldType }}">
                <label class="form-label fw-semibold" for="{{ $inputId }}" style="font-size:0.85rem;color:var(--text-700);">
                    {{ $field['label'] ?? 'Field ' . ($idx + 1) }}
                    @if($isRequired)<span class="text-danger ms-1">*</span>@endif
                </label>

                @if(!empty($field['description']))
                    <div class="mb-2" style="font-size:0.78rem;color:var(--text-400);">
                        <i class="fas fa-info-circle me-1"></i>{{ $field['description'] }}
                    </div>
                @endif

                @if($fieldType === 'integer_scale')
                    {{-- Integer scale 1-7 --}}
                    <div class="d-flex flex-wrap gap-2 mt-1" id="{{ $inputId }}_btns">
                        @for($n = 1; $n <= 7; $n++)
                            <button type="button"
                                    class="scale-btn btn btn-sm fw-bold"
                                    data-value="{{ $n }}"
                                    data-target="{{ $inputId }}"
                                    onclick="selectScale('{{ $inputId }}', {{ $n }})"
                                    style="width:42px;height:42px;border-radius:10px;font-size:0.9rem;
                                           background:{{ $savedVal == $n ? 'var(--primary-700)' : '#f3f4f6' }};
                                           color:{{ $savedVal == $n ? '#fff' : 'var(--text-700)' }};
                                           border:2px solid {{ $savedVal == $n ? 'var(--primary-700)' : 'transparent' }};
                                           transition:all 0.15s;">
                                {{ $n }}
                            </button>
                        @endfor
                    </div>
                    <div style="font-size:0.72rem;color:var(--text-400);margin-top:5px;">
                        1 = Sangat Kurang &nbsp;·&nbsp; 7 = Sangat Baik
                    </div>
                    <input type="hidden"
                           id="{{ $inputId }}"
                           name="{{ $inputName }}"
                           value="{{ $savedVal }}"
                           {{ $isRequired ? 'required' : '' }}>

                @else
                    {{-- Textarea --}}
                    <textarea class="form-control form-control-sm"
                              id="{{ $inputId }}"
                              name="{{ $inputName }}"
                              rows="4"
                              style="border-radius:8px;resize:vertical;"
                              placeholder="Isi di sini..."
                              {{ $isRequired ? 'required' : '' }}>{{ $savedVal }}</textarea>
                @endif
            </div>
        @endforeach
    </div>
</div>

@push('scripts')
<script>
function selectScale(inputId, val) {
    document.getElementById(inputId).value = val;
    // Update button styles
    document.querySelectorAll(`[data-target="${inputId}"]`).forEach(btn => {
        const isSelected = parseInt(btn.dataset.value) === val;
        btn.style.background      = isSelected ? 'var(--primary-700)' : '#f3f4f6';
        btn.style.color           = isSelected ? '#fff' : 'var(--text-700)';
        btn.style.borderColor     = isSelected ? 'var(--primary-700)' : 'transparent';
        btn.style.transform       = isSelected ? 'scale(1.08)' : 'scale(1)';
    });
}
</script>
@endpush
@endif
