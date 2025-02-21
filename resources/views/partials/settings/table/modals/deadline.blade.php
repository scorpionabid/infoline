<!-- Son Tarix Modal -->
<div class="modal fade" id="deadlineModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Son Tarix Təyin Et</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="deadlineForm">
                <div class="modal-body">
                    <input type="hidden" name="column_id" id="deadlineColumnId">
                    
                    <div class="mb-3">
                        <label class="form-label">Son Tarix</label>
                        <input type="date" class="form-control" name="end_date" id="columnEndDate" required>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> Son tarixdən sonra bu sütuna məlumat daxil etmək mümkün olmayacaq.
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