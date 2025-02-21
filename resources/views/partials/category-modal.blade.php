<!-- Kateqoriya Əlavə Et Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Yeni Kateqoriya</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addCategoryForm" action="{{ route('settings.table.category.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <!-- Form xətaları -->
                    <div class="form-errors alert alert-danger" style="display: none;"></div>

                    <!-- Kateqoriya adı -->
                    <div class="mb-3">
                        <label for="categoryName" class="form-label">Kateqoriya adı <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="categoryName" name="name" required>
                        <div class="invalid-feedback">
                            Kateqoriya adı minimum 2 simvol olmalıdır
                        </div>
                    </div>

                    <!-- Kateqoriya təsviri -->
                    <div class="mb-3">
                        <label for="categoryDescription" class="form-label">Təsvir</label>
                        <textarea class="form-control" id="categoryDescription" name="description" rows="3"></textarea>
                        <div class="form-text">
                            Maximum 1000 simvol
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Ləğv et</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Yadda saxla
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Kateqoriya Redaktə Et Modal -->
<div class="modal fade" id="editCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kateqoriyanı Redaktə Et</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editCategoryForm" action="" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" id="editCategoryId" name="id">
                <div class="modal-body">
                    <!-- Form xətaları -->
                    <div class="form-errors alert alert-danger" style="display: none;"></div>

                    <!-- Kateqoriya adı -->
                    <div class="mb-3">
                        <label for="editCategoryName" class="form-label">Kateqoriya adı <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="editCategoryName" name="name" required>
                        <div class="invalid-feedback">
                            Kateqoriya adı minimum 2 simvol olmalıdır
                        </div>
                    </div>

                    <!-- Kateqoriya təsviri -->
                    <div class="mb-3">
                        <label for="editCategoryDescription" class="form-label">Təsvir</label>
                        <textarea class="form-control" id="editCategoryDescription" name="description" rows="3"></textarea>
                        <div class="form-text">
                            Maximum 1000 simvol
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="editCategoryStatus" name="status" checked>
                            <label class="form-check-label" for="editCategoryStatus">Aktiv</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Ləğv et</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Yadda saxla
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Kateqoriya Sil Modal -->
<div class="modal fade" id="deleteCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kateqoriyanı Sil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Bu kateqoriyanı silmək istədiyinizə əminsiniz?</p>
                <p class="text-danger">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    Bu əməliyyat geri qaytarıla bilməz və kateqoriyaya aid bütün sütunlar silinəcək.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Ləğv et</button>
                <form id="deleteCategoryForm" class="d-inline" action="" method="POST">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" id="deleteCategoryId" name="id">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i> Sil
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Kateqoriya əlavə
document.getElementById('addCategoryForm').addEventListener('submit', function(event) {
    event.preventDefault();
    const formData = new FormData(this);
    fetch(this.action, {
        method: this.method,
        body: formData,
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Modal-ı bağla
            const modal = bootstrap.Modal.getInstance(document.getElementById('addCategoryModal'));
            modal.hide();
            // Formu sıfırla
            this.reset();
        } else {
            alert('Xəta baş verdi!');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Xəta baş verdi!');
    });
});

// Kateqoriya redaktə
function editCategory(id) {
    fetch(`/api/v1/categories/${id}`)
        .then(response => response.json())
        .then(data => {
            const form = document.getElementById('editCategoryForm');
            form.action = `/settings/table/category/${id}`;
            form.querySelector('[name="name"]').value = data.name;
            form.querySelector('[name="description"]').value = data.description || '';
            form.querySelector('[name="status"]').checked = data.status;
            form.querySelector('#editCategoryId').value = data.id;
            
            // Modal-ı göstər
            new bootstrap.Modal(document.getElementById('editCategoryModal')).show();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Xəta baş verdi!');
        });
}

// Kateqoriya redaktə
document.getElementById('editCategoryForm').addEventListener('submit', function(event) {
    event.preventDefault();
    const formData = new FormData(this);
    fetch(this.action, {
        method: this.method,
        body: formData,
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Modal-ı bağla
            const modal = bootstrap.Modal.getInstance(document.getElementById('editCategoryModal'));
            modal.hide();
        } else {
            alert('Xəta baş verdi!');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Xəta baş verdi!');
    });
});

// Kateqoriya sil
document.getElementById('deleteCategoryForm').addEventListener('submit', function(event) {
    event.preventDefault();
    const formData = new FormData(this);
    fetch(this.action, {
        method: this.method,
        body: formData,
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Modal-ı bağla
            const modal = bootstrap.Modal.getInstance(document.getElementById('deleteCategoryModal'));
            modal.hide();
        } else {
            alert('Xəta baş verdi!');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Xəta baş verdi!');
    });
});

// Modal bağlandıqda formu sıfırla
document.getElementById('addCategoryModal').addEventListener('hidden.bs.modal', function () {
    const form = document.getElementById('addCategoryForm');
    form.reset();
});

document.getElementById('editCategoryModal').addEventListener('hidden.bs.modal', function () {
    const form = document.getElementById('editCategoryForm');
    form.reset();
    form.action = "";
});

document.getElementById('deleteCategoryModal').addEventListener('hidden.bs.modal', function () {
    const form = document.getElementById('deleteCategoryForm');
    form.reset();
    form.action = "";
});
</script>
@endpush