<div class="modal fade" id="createAdminModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Yeni Admin Yarat</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="createAdminForm" method="POST">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="school_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="first_name" class="form-label">Ad <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="first_name" name="first_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="last_name" class="form-label">Soyad <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="last_name" name="last_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Telefon <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="phone" name="phone" required>
                    </div>
                    <div class="mb-3">
                        <label for="utis_code" class="form-label">UTİS Kodu <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="utis_code" name="utis_code" required maxlength="7" pattern="\d{7}">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Şifrə <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Şifrə təkrarı <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Ləğv et</button>
                    <button type="submit" class="btn btn-primary">Yarat</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Create admin modal
    const createAdminModal = document.getElementById('createAdminModal');
    if (createAdminModal) {
        createAdminModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const schoolId = button.getAttribute('data-school-id');
            const form = this.querySelector('#createAdminForm');
            
            // Set form action URL
            form.action = `/settings/personal/schools/${schoolId}/admins`;
        });

        // Handle form submission
        const createAdminForm = document.getElementById('createAdminForm');
        createAdminForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            fetch(this.action, {
                method: 'POST',
                body: new FormData(this),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    toastr.success(data.message);
                    
                    // Close modal and refresh page
                    bootstrap.Modal.getInstance(createAdminModal).hide();
                    window.location.reload();
                } else {
                    toastr.error(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                toastr.error('Xəta baş verdi');
            });
        });
    }
});
</script>
<?php $__env->stopPush(); ?>
<?php /**PATH /Users/home/Library/CloudStorage/OneDrive-BureauonICTforEducation,MinistryofEducation/infoline_app/resources/views/pages/settings/personal/schools/admin_modal.blade.php ENDPATH**/ ?>