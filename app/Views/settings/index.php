<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid py-4">
    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4" id="settingsTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="columns-tab" data-bs-toggle="tab" href="#columns" role="tab">
                <i class="fas fa-table"></i> Sütunlar
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="schools-tab" data-bs-toggle="tab" href="#schools" role="tab">
                <i class="fas fa-school"></i> Məktəblər
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="admins-tab" data-bs-toggle="tab" href="#admins" role="tab">
                <i class="fas fa-users-cog"></i> Məktəb Adminləri
            </a>
        </li>
    </ul>

    <!-- Tab Contents -->
    <div class="tab-content" id="settingsTabContent">
        <!-- Sütunlar Tab -->
        <div class="tab-pane fade show active" id="columns" role="tabpanel">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Sütunlar</h5>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#columnModal">
                        <i class="fas fa-plus"></i> Yeni Sütun
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="columnsTable">
                            <thead>
                                <tr>
                                    <th>Sütun Adı</th>
                                    <th>Məlumat Tipi</th>
                                    <th>Son Tarix</th>
                                    <th>Status</th>
                                    <th>Əməliyyatlar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($columns as $column): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($column['name']); ?></td>
                                    <td><?php echo htmlspecialchars($column['type']); ?></td>
                                    <td><?php echo $column['deadline'] ? date('d.m.Y H:i', strtotime($column['deadline'])) : '-'; ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $column['is_active'] ? 'success' : 'danger'; ?>">
                                            <?php echo $column['is_active'] ? 'Aktiv' : 'Deaktiv'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-primary edit-column" data-id="<?php echo $column['id']; ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger delete-column" data-id="<?php echo $column['id']; ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Məktəblər Tab -->
        <div class="tab-pane fade" id="schools" role="tabpanel">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Məktəblər</h5>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#schoolModal">
                        <i class="fas fa-plus"></i> Yeni Məktəb
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="schoolsTable">
                            <thead>
                                <tr>
                                    <th>Məktəb Adı</th>
                                    <th>Admin Sayı</th>
                                    <th>Əməliyyatlar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($schools as $school): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($school['name']); ?></td>
                                    <td><?php echo $school['admin_count'] ?? 0; ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary edit-school" data-id="<?php echo $school['id']; ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger delete-school" data-id="<?php echo $school['id']; ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Məktəb Adminləri Tab -->
        <div class="tab-pane fade" id="admins" role="tabpanel">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Məktəb Adminləri</h5>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#adminModal">
                        <i class="fas fa-plus"></i> Yeni Admin
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="adminsTable">
                            <thead>
                                <tr>
                                    <th>Ad Soyad</th>
                                    <th>İstifadəçi Adı</th>
                                    <th>Məktəb</th>
                                    <th>Status</th>
                                    <th>Əməliyyatlar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($schoolAdmins as $admin): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($admin['name']); ?></td>
                                    <td><?php echo htmlspecialchars($admin['username']); ?></td>
                                    <td><?php echo htmlspecialchars($admin['school_name']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $admin['is_active'] ? 'success' : 'danger'; ?>">
                                            <?php echo $admin['is_active'] ? 'Aktiv' : 'Deaktiv'; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-primary edit-admin" data-id="<?php echo $admin['id']; ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger delete-admin" data-id="<?php echo $admin['id']; ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sütun Modal -->
<div class="modal fade" id="columnModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sütun Əlavə Et/Düzəlt</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="columnForm">
                    <input type="hidden" name="id" id="column_id">
                    <div class="mb-3">
                        <label class="form-label">Sütun Adı</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Məlumat Tipi</label>
                        <select class="form-select" name="type" required>
                            <option value="text">Mətn</option>
                            <option value="number">Rəqəm</option>
                            <option value="date">Tarix</option>
                            <option value="select">Seçim</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Son Tarix</label>
                        <input type="datetime-local" class="form-control" name="deadline">
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="is_active" id="column_is_active" checked>
                            <label class="form-check-label">Aktiv</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bağla</button>
                <button type="button" class="btn btn-primary" id="saveColumn">Yadda Saxla</button>
            </div>
        </div>
    </div>
</div>

<!-- Məktəb Modal -->
<div class="modal fade" id="schoolModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Məktəb Əlavə Et/Düzəlt</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="schoolForm">
                    <input type="hidden" name="id" id="school_id">
                    <div class="mb-3">
                        <label class="form-label">Məktəb Adı</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bağla</button>
                <button type="button" class="btn btn-primary" id="saveSchool">Yadda Saxla</button>
            </div>
        </div>
    </div>
</div>

<!-- Admin Modal -->
<div class="modal fade" id="adminModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Admin Əlavə Et/Düzəlt</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="adminForm">
                    <input type="hidden" name="id" id="admin_id">
                    <div class="mb-3">
                        <label class="form-label">Məktəb</label>
                        <select class="form-select" name="school_id" required>
                            <?php foreach($schools as $school): ?>
                            <option value="<?php echo $school['id']; ?>">
                                <?php echo htmlspecialchars($school['name']); ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Ad Soyad</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">İstifadəçi Adı</label>
                        <input type="text" class="form-control" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Şifrə</label>
                        <input type="password" class="form-control" name="password" id="admin_password">
                        <small class="text-muted">Düzəliş zamanı boş buraxsanız, şifrə dəyişməyəcək</small>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="is_active" id="admin_is_active" checked>
                            <label class="form-check-label">Aktiv</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bağla</button>
                <button type="button" class="btn btn-primary" id="saveAdmin">Yadda Saxla</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Təsdiq</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Bu məlumatı silmək istədiyinizə əminsiniz?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Xeyr</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Bəli, Sil</button>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

<!-- Custom JS -->
<script>
$(document).ready(function() {
    // DataTables initialization
    $('#columnsTable, #schoolsTable, #adminsTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/az.json'
        }
    });

    // Sütun əməliyyatları
    $('#saveColumn').click(function() {
        var form = $('#columnForm');
        var id = $('#column_id').val();
        var url = id ? '/settings/updateColumn' : '/settings/addColumn';
        
        $.post(url, form.serialize())
            .done(function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Xəta: ' + response.error);
                }
            });
    });

    // Məktəb əməliyyatları
    $('#saveSchool').click(function() {
        var form = $('#schoolForm');
        var id = $('#school_id').val();
        var url = id ? '/settings/updateSchool' : '/settings/addSchool';
        
        $.post(url, form.serialize())
            .done(function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Xəta: ' + response.error);
                }
            });
    });

    // Admin əməliyyatları
    $('#saveAdmin').click(function() {
        var form = $('#adminForm');
        var id = $('#admin_id').val();
        var url = id ? '/settings/updateSchoolAdmin' : '/settings/addSchoolAdmin';
        
        $.post(url, form.serialize())
            .done(function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Xəta: ' + response.error);
                }
            });
    });

    // Edit events
    $('.edit-column').click(function() {
        var id = $(this).data('id');
        // TODO: Load column data and show modal
    });

    $('.edit-school').click(function() {
        var id = $(this).data('id');
        // TODO: Load school data and show modal
    });

    $('.edit-admin').click(function() {
        var id = $(this).data('id');
        // TODO: Load admin data and show modal
    });

    // Delete events
    $('.delete-column, .delete-school, .delete-admin').click(function() {
        var id = $(this).data('id');
        var type = $(this).hasClass('delete-column') ? 'column' : 
                  $(this).hasClass('delete-school') ? 'school' : 'admin';
        
        $('#deleteModal').data('id', id).data('type', type).modal('show');
    });

    $('#confirmDelete').click(function() {
        var modal = $('#deleteModal');
        var id = modal.data('id');
        var type = modal.data('type');
        var url = '/settings/delete' + type.charAt(0).toUpperCase() + type.slice(1);
        
        $.post(url, {id: id})
            .done(function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    alert('Xəta: ' + response.error);
                }
            });
    });
});
</script>