<!-- Kateqoriya Kopyalama Modal -->
<div class="modal fade" id="cloneCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Kateqoriyanı Kopyala</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="cloneCategoryForm">
                <div class="modal-body">
                    <input type="hidden" name="source_category_id" id="sourceCategoryId">
                    
                    <div class="mb-3">
                        <label class="form-label">Yeni Kateqoriya Adı</label>
                        <input type="text" class="form-control" name="name" id="cloneCategoryName" required>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Təsvir</label>
                        <textarea class="form-control" name="description" id="cloneCategoryDescription" rows="3"></textarea>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Kateqoriyanı kopyaladıqda bütün sütunlar da kopyalanacaq.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Ləğv et</button>
                    <button type="submit" class="btn btn-primary">Kopyala</button>
                </div>
            </form>
        </div>
    </div>
</div>