<!-- Kateqoriya Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="categoryModalTitle">Yeni Kateqoriya</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="categoryForm">
                <div class="modal-body">
                    <input type="hidden" name="category_id" id="categoryId">
                    
                    <!-- Əsas Məlumatlar -->
                    <div class="mb-3">
                        <label class="form-label">Kateqoriya Adı</label>
                        <input type="text" class="form-control" name="name" id="categoryName" required>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Təsvir</label>
                        <textarea class="form-control" name="description" id="categoryDescription" rows="3"></textarea>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Növ</label>
                        <select class="form-select" name="type" id="categoryType" required>
                            <option value="standard">Standart</option>
                            <option value="dynamic">Dinamik</option>
                            <option value="report">Hesabat</option>
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>

                    <!-- Status (yalnız redaktə zamanı) -->
                    <div class="mb-3 d-none" id="statusGroup">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="status" id="categoryStatus">
                            <label class="form-check-label">Aktiv</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Ləğv et</button>
                    <button type="submit" class="btn btn-primary">Yadda saxla</button>
                </div>
            </form>
        </div>
    </div>
</div>