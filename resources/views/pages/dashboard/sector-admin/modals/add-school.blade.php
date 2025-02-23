{{-- resources/views/pages/dashboard/sector-admin/modals/add-school.blade.php --}}
<div class="modal fade" id="addSchoolModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="addSchoolForm">
                <div class="modal-header">
                    <h5 class="modal-title">Yeni məktəb əlavə et</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Məktəb adı</label>
                        <input type="text" 
                               class="form-control" 
                               name="name" 
                               required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">UTIS kodu</label>
                        <input type="text" 
                               class="form-control" 
                               name="utis_code" 
                               required 
                               pattern="[0-9]{7}">
                        <div class="form-text">7 rəqəmli UTIS kodu daxil edin</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" 
                               class="form-control" 
                               name="email">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Telefon</label>
                        <input type="tel" 
                               class="form-control" 
                               name="phone" 
                               pattern="\+994[0-9]{9}">
                        <div class="form-text">Format: +994XXXXXXXXX</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" 
                            class="btn btn-secondary" 
                            data-bs-dismiss="modal">İmtina</button>
                    <button type="submit" 
                            class="btn btn-primary">
                        <i class="fas fa-save"></i> Yadda saxla
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>