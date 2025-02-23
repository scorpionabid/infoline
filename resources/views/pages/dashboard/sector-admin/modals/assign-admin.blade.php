{{-- resources/views/pages/dashboard/sector-admin/modals/assign-admin.blade.php --}}
<div class="modal fade" id="assignAdminModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="assignAdminForm">
                <div class="modal-header">
                    <h5 class="modal-title">Məktəb admini təyin et</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="school_id" id="assignSchoolId">
                    
                    <div class="mb-3">
                        <label class="form-label">Admin tipi</label>
                        <select class="form-select" name="admin_type" id="adminType">
                            <option value="new">Yeni admin</option>
                            <option value="existing">Mövcud admin</option>
                        </select>
                    </div>

                    {{-- Mövcud admin seçimi --}}
                    <div id="existingAdminSection" class="d-none">
                        <div class="mb-3">
                            <label class="form-label">Admin seçin</label>
                            <select class="form-select" name="existing_admin_id">
                                <option value="">Seçin...</option>
                                @foreach($availableAdmins ?? [] as $admin)
                                    <option value="{{ $admin->id }}">{{ $admin->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Yeni admin məlumatları --}}
                    <div id="newAdminSection">
                        <div class="mb-3">
                            <label class="form-label">Ad Soyad</label>
                            <input type="text" class="form-control" name="name">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">İstifadəçi adı</label>
                            <input type="text" class="form-control" name="username">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Şifrə</label>
                            <input type="password" class="form-control" name="password">
                        </div>
                        <div class="form-check mb-3">
                            <input type="checkbox" 
                                   class="form-check-input" 
                                   name="send_credentials" 
                                   id="sendCredentials" 
                                   checked>
                            <label class="form-check-label" for="sendCredentials">
                                Giriş məlumatları emailə göndərilsin
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" 
                            class="btn btn-secondary" 
                            data-bs-dismiss="modal">İmtina</button>
                    <button type="submit" 
                            class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Təyin et
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>