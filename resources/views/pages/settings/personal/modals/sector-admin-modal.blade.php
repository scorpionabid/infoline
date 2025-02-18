<div class="modal fade" id="sectorAdminModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sektor Admini Təyin Et</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="sectorAdminForm">
                @csrf
                <input type="hidden" id="sectorIdInput" name="sector_id">
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label>Ad</label>
                        <input type="text" name="first_name" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>Soyad</label>
                        <input type="text" name="last_name" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>İstifadəçi adı</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="form-group mb-3">
                        <label>UTIS Kodu</label>
                        <input type="text" name="utis_code" class="form-control" required 
                               pattern="[0-9]{7}" maxlength="7">
                    </div>
                    <div class="form-group mb-3">
                        <label>Şifrə</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bağla</button>
                    <button type="submit" class="btn btn-primary">Təyin et</button>
                </div>
            </form>
        </div>
    </div>
</div>