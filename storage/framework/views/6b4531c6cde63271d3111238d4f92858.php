<!-- Limit Modal -->
<div class="modal fade" id="limitModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Daxiletmə Limiti</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="limitForm">
                <div class="modal-body">
                    <input type="hidden" name="column_id" id="limitColumnId">
                    
                    <div class="mb-3">
                        <label class="form-label">Maksimum Daxiletmə Sayı</label>
                        <input type="number" class="form-control" name="input_limit" id="columnInputLimit" min="1" required>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Bu limit dolduqdan sonra sütuna yeni məlumat daxil etmək mümkün olmayacaq.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Ləğv et</button>
                    <button type="submit" class="btn btn-primary">Yadda saxla</button>
                </div>
            </form>
        </div>
    </div>
</div><?php /**PATH /Users/home/Library/CloudStorage/OneDrive-BureauonICTforEducation,MinistryofEducation/infoline_app/resources/views/partials/settings/table/modals/limit.blade.php ENDPATH**/ ?>