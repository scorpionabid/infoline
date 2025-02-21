<div class="modal fade" id="regionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Yeni Region</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="regionForm">
                    <?php echo csrf_field(); ?>
                    <div id="regionFormErrors"></div>
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Region Adı <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>

                    <div class="mb-3">
                        <label for="code" class="form-label">Region Kodu <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="code" name="code" required>
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label">Telefon <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="phone" name="phone" required>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Təsvir</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Ləğv Et</button>
                <button type="submit" class="btn btn-primary" form="regionForm">Yadda Saxla</button>
            </div>
        </div>
    </div>
</div><?php /**PATH /Users/home/Library/CloudStorage/OneDrive-BureauonICTforEducation,MinistryofEducation/infoline_app/resources/views/pages/settings/personal/regions/create.blade.php ENDPATH**/ ?>