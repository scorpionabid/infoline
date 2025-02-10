<!-- Kateqoriya Əlavə/Redaktə Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="categoryModalTitle">Yeni Kateqoriya</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="categoryForm" action="{{ route('settings.table.category.store') }}" method="POST" class="needs-validation" novalidate>
                @csrf
                <input type="hidden" name="category_id" id="categoryId">

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Kateqoriya adı <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="name" required
                               minlength="2" maxlength="255">
                        <div class="invalid-feedback">
                            Kateqoriya adı minimum 2 simvol olmalıdır
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Açıqlama</label>
                        <textarea class="form-control" name="description" rows="3"
                                maxlength="1000"></textarea>
                        <div class="form-text">
                            Maximum 1000 simvol
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Ləğv et</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>
                        <span id="categorySubmitText">Əlavə et</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Kateqoriya redaktə
function editCategory(id) {
    fetch(`/api/v1/categories/${id}`)
        .then(response => response.json())
        .then(data => {
            const form = document.getElementById('categoryForm');
            form.querySelector('[name="name"]').value = data.name;
            form.querySelector('[name="description"]').value = data.description || '';
            form.querySelector('#categoryId').value = data.id;
            
            // Form action və method yenilə
            form.action = `/settings/table/category/${id}`;
            form.insertAdjacentHTML('beforeend', `@method('PUT')`);
            
            // Modal başlıq və button mətnini yenilə
            document.getElementById('categoryModalTitle').textContent = 'Kateqoriya Redaktəsi';
            document.getElementById('categorySubmitText').textContent = 'Yadda saxla';
            
            // Modal-ı göstər
            new bootstrap.Modal(document.getElementById('addCategoryModal')).show();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Xəta baş verdi!');
        });
}

// Modal bağlandıqda formu sıfırla
document.getElementById('addCategoryModal').addEventListener('hidden.bs.modal', function () {
    const form = document.getElementById('categoryForm');
    form.reset();
    form.action = "{{ route('settings.table.category.store') }}";
    form.querySelector('#categoryId').value = '';
    
    // PUT method-u varsa sil
    const methodInput = form.querySelector('input[name="_method"]');
    if (methodInput) methodInput.remove();
    
    // Başlıq və button mətnini default-a qaytar
    document.getElementById('categoryModalTitle').textContent = 'Yeni Kateqoriya';
    document.getElementById('categorySubmitText').textContent = 'Əlavə et';
});
</script>
@endpush