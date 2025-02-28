<!-- Son Tarix Modal -->
<div class="modal fade" id="deadlineModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="deadlineForm" method="POST">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="_method" value="PATCH">
                <input type="hidden" name="column_id" id="deadlineColumnId">
                
                <div class="modal-header">
                    <h5 class="modal-title">Son Tarixi Yenilə</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-errors alert alert-danger" style="display: none;"></div>
                    
                    <div class="mb-3">
                        <label for="deadlineDate" class="form-label">Yeni son tarix</label>
                        <input type="date" class="form-control" id="deadlineDate" name="deadline_date" required>
                        <div class="form-text">Bu tarixdən sonra sütun deaktiv olacaq</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bağla</button>
                    <button type="submit" class="btn btn-primary">Yadda saxla</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Form submit hadisəsi
        const deadlineForm = document.getElementById('deadlineForm');
        if (deadlineForm) {
            deadlineForm.addEventListener('submit', function(event) {
                DeadlineOperations.submitForm(event);
            });
        }
    });
</script>
<?php /**PATH /Users/home/Library/CloudStorage/OneDrive-BureauonICTforEducation,MinistryofEducation/infoline_app/resources/views/partials/settings/table/modals/deadline.blade.php ENDPATH**/ ?>