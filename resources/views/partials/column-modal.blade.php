<!-- Sütun Əlavə/Redaktə Modal -->
<div class="modal fade" id="addColumnModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="columnModalTitle">Yeni Sütun</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="columnForm" action="{{ route('settings.table.column.store') }}" method="POST" class="needs-validation" novalidate>
                @csrf
                <input type="hidden" name="category_id" value="{{ $selectedCategory?->id }}">
                <input type="hidden" name="column_id" id="columnId">

                <div class="modal-body">
                    <!-- Əsas məlumatlar -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Sütun adı <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="name" required
                                   minlength="2" maxlength="255">
                            <div class="invalid-feedback">
                                Sütun adı minimum 2 simvol olmalıdır
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Məlumat tipi <span class="text-danger">*</span></label>
                            <select class="form-select" name="data_type" id="dataType" required>
                                <option value="">Seçin</option>
                                <option value="text">Mətn</option>
                                <option value="number">Rəqəm</option>
                                <option value="date">Tarix</option>
                                <option value="select">Tək seçim</option>
                                <option value="multiselect">Çoxlu seçim</option>
                                <option value="file">Fayl</option>
                            </select>
                            <div class="invalid-feedback">
                                Məlumat tipi seçilməlidir
                            </div>
                        </div>
                    </div>

                    <!-- Tarix və limit -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Son tarix</label>
                            <input type="date" class="form-control" name="end_date"
                                   min="{{ date('Y-m-d') }}">
                            <div class="form-text">
                                Daxiletmənin son tarixi
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Daxiletmə limiti</label>
                            <input type="number" class="form-control" name="input_limit"
                                   min="1" step="1">
                            <div class="form-text">
                                Maksimum daxil edilə biləcək məlumat sayı
                            </div>
                        </div>
                    </div>

                    <!-- Seçim variantları -->
                    <div id="choicesSection" class="d-none">
                        <div class="card mb-3">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">Seçim variantları</h6>
                                <button type="button" class="btn btn-sm btn-primary" onclick="addChoice()">
                                    <i class="fas fa-plus me-1"></i>
                                    Variant əlavə et
                                </button>
                            </div>
                            <div class="card-body">
                                <div id="choicesList"></div>
                                <div class="invalid-feedback">
                                    Minimum 2 seçim variantı əlavə edilməlidir
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Validasiya qaydaları -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0">Validasiya qaydaları</h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" 
                                               name="validation_rules[required]" id="isRequired">
                                        <label class="form-check-label" for="isRequired">
                                            Məcburidir
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                               name="validation_rules[unique]" id="isUnique">
                                        <label class="form-check-label" for="isUnique">
                                            Unikal olmalıdır
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Tip-ə məxsus validasiya -->
                            <div id="typeSpecificValidation"></div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Ləğv et</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>
                        <span id="columnSubmitText">Əlavə et</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function editColumn(id) {
    fetch(`/api/v1/columns/${id}`)
        .then(response => response.json())
        .then(data => {
            const form = document.getElementById('columnForm');
            // Əsas məlumatlar
            form.querySelector('[name="name"]').value = data.name;
            form.querySelector('[name="data_type"]').value = data.data_type;
            form.querySelector('[name="end_date"]').value = data.end_date || '';
            form.querySelector('[name="input_limit"]').value = data.input_limit || '';
            form.querySelector('#columnId').value = data.id;
            
            // Validasiya qaydaları
            if (data.validation_rules) {
                Object.entries(data.validation_rules).forEach(([key, value]) => {
                    const input = form.querySelector(`[name="validation_rules[${key}]"]`);
                    if (input) input.checked = value;
                });
            }
            
            // Seçim variantları
            if (data.choices && data.choices.length > 0) {
                data.choices.forEach(choice => addChoice(choice.value));
            }
            
            // Form action və method yenilə
            form.action = `/settings/table/column/${id}`;
            form.insertAdjacentHTML('beforeend', `@method('PUT')`);
            
            // Modal başlıq və button mətnini yenilə
            document.getElementById('columnModalTitle').textContent = 'Sütun Redaktəsi';
            document.getElementById('columnSubmitText').textContent = 'Yadda saxla';
            
            // Tip-ə uyğun bölmələri göstər
            toggleChoicesSection();
            updateTypeSpecificValidation();
            
            // Modal-ı göstər
            new bootstrap.Modal(document.getElementById('addColumnModal')).show();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Xəta baş verdi!');
        });
}

// Modal bağlandıqda formu sıfırla
document.getElementById('addColumnModal').addEventListener('hidden.bs.modal', function () {
    const form = document.getElementById('columnForm');
    form.reset();
    form.action = "{{ route('settings.table.column.store') }}";
    form.querySelector('#columnId').value = '';
    
    // PUT method-u varsa sil
    const methodInput = form.querySelector('input[name="_method"]');
    if (methodInput) methodInput.remove();
    
    // Seçim variantlarını təmizlə
    document.getElementById('choicesList').innerHTML = '';
    
    // Başlıq və button mətnini default-a qaytar
    document.getElementById('columnModalTitle').textContent = 'Yeni Sütun';
    document.getElementById('columnSubmitText').textContent = 'Əlavə et';
});

// Seçim variantı əlavə et
function addChoice(value = '') {
    const choicesList = document.getElementById('choicesList');
    const choiceId = Date.now();
    
    const html = `
        <div class="input-group mb-2" id="choice_${choiceId}">
            <input type="text" class="form-control" name="choices[]" required
                   value="${value}" placeholder="Variant daxil edin">
            <button type="button" class="btn btn-outline-danger" onclick="removeChoice(${choiceId})">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    choicesList.insertAdjacentHTML('beforeend', html);
}

// Seçim variantını sil
function removeChoice(choiceId) {
    document.getElementById(`choice_${choiceId}`).remove();
}
</script>
@endpush