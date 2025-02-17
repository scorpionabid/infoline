<div class="modal fade" id="sectorAdminModal" tabindex="-1" aria-labelledby="sectorAdminModalLabel">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sectorAdminModalLabel">Sektor Admini Təyin Etmə</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="sectorAdminForm" method="POST" action="{{ route('settings.personal.sectors.admin', ':id') }}">
                @csrf
                <input type="hidden" name="sector_id" id="sectorIdInput">
                <input type="hidden" name="user_type" value="sectoradmin">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Ad</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">İstifadəçi adı</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Şifrə</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                    </div>

                    <div class="card mt-3">
                        <div class="card-header">Sektor Adminləri</div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table id="sectorAdminTable" class="table table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th>Ad</th>
                                            <th>İstifadəçi adı</th>
                                            <th>Əməliyyatlar</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>
                        </div>
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