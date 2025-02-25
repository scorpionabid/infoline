<!-- Edit Admin Modal -->
<div class="modal fade" id="editAdminModal" tabindex="-1" aria-labelledby="editAdminModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editAdminModalLabel">Məktəb Administratorunu Redaktə Et</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editAdminForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_first_name" class="form-label">Ad <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_first_name" name="first_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_last_name" class="form-label">Soyad <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_last_name" name="last_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="edit_email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_phone" class="form-label">Telefon <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_phone" name="phone" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_utis_code" class="form-label">UTİS Kodu <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_utis_code" name="utis_code" required maxlength="7" pattern="\d{7}">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bağla</button>
                    <button type="submit" class="btn btn-primary">Yadda Saxla</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Edit admin modal
    const editAdminModal = document.getElementById('editAdminModal');
    if (editAdminModal) {
        editAdminModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const adminId = button.getAttribute('data-admin-id');
            const schoolId = button.getAttribute('data-school-id');
            const form = this.querySelector('#editAdminForm');
            
            // Set form action URL
            form.action = `/settings/personal/schools/${schoolId}/admins/${adminId}`;
            
            // Fetch admin data
            fetch(`/settings/personal/schools/admins/${adminId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const admin = data.data;
                        form.querySelector('#edit_first_name').value = admin.first_name;
                        form.querySelector('#edit_last_name').value = admin.last_name;
                        form.querySelector('#edit_email').value = admin.email;
                        form.querySelector('#edit_phone').value = admin.phone;
                        form.querySelector('#edit_utis_code').value = admin.utis_code;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Məlumatları əldə edərkən xəta baş verdi');
                });
        });
    }
});
</script>
@endpush
