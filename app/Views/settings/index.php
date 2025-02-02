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
                                    <td data-school-id="<?php echo $admin['school_id']; ?>"><?php echo htmlspecialchars($admin['school_name']); ?></td>
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

<!-- Import Buttons -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Məktəblərin İmportu</h5>
                <p class="card-text">Excel faylından məktəbləri import edin.</p>
                <form id="importSchoolsForm" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="schoolsFile" class="form-label">Excel Faylı</label>
                        <input type="file" class="form-control" id="schoolsFile" name="file" accept=".xlsx,.xls">
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-file-import"></i> İmport Et
                    </button>
                    <a href="/settings/downloadTemplate/schools" class="btn btn-outline-secondary">
                        <i class="fas fa-download"></i> Şablon
                    </a>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Məktəb Adminlərinin İmportu</h5>
                <p class="card-text">Excel faylından məktəb adminlərini import edin.</p>
                <form id="importAdminsForm" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="adminsFile" class="form-label">Excel Faylı</label>
                        <input type="file" class="form-control" id="adminsFile" name="file" accept=".xlsx,.xls">
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-file-import"></i> İmport Et
                    </button>
                    <a href="/settings/downloadTemplate/admins" class="btn btn-outline-secondary">
                        <i class="fas fa-download"></i> Şablon
                    </a>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Sütun Modal -->
<div class="modal fade" id="columnModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sütun Əlavə Et</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="columnModalAlert"></div>
                <form id="columnForm">
                    <input type="hidden" name="id">
                    <div class="mb-3">
                        <label for="columnName" class="form-label">Sütun Adı</label>
                        <input type="text" class="form-control" id="columnName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="columnType" class="form-label">Məlumat Tipi</label>
                        <select class="form-control" id="columnType" name="type" required>
                            <option value="text">Mətn</option>
                            <option value="number">Rəqəm</option>
                            <option value="date">Tarix</option>
                            <option value="file">Fayl</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="columnDeadline" class="form-label">Son Tarix</label>
                        <input type="datetime-local" class="form-control" id="columnDeadline" name="deadline">
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="columnIsActive" name="is_active" checked>
                        <label class="form-check-label" for="columnIsActive">Aktiv</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bağla</button>
                <button type="submit" form="columnForm" class="btn btn-primary">Yadda Saxla</button>
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
            <form id="schoolForm">
                <div class="modal-body">
                    <div id="schoolModalAlert"></div>
                    <input type="hidden" name="id" id="school_id">
                    <div class="mb-3">
                        <label class="form-label">Məktəb Adı</label>
                        <input type="text" class="form-control" name="name" required>
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
                <button type="submit" form="adminForm" class="btn btn-primary">Yadda Saxla</button>
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
    const dataTablesAz = {
        "emptyTable": "Cədvəldə heç bir məlumat yoxdur",
        "info": "_TOTAL_ Nəticədən _START_ - _END_ Arası Nəticələr",
        "infoEmpty": "Nəticə Yoxdur",
        "infoFiltered": "(_MAX_ Nəticə İçindən Tapılanlar)",
        "lengthMenu": "Səhifədə _MENU_ Nəticə Göstər",
        "loadingRecords": "Yüklənir...",
        "processing": "Gözləyin...",
        "search": "Axtarış:",
        "zeroRecords": "Nəticə Tapılmadı.",
        "paginate": {
            "first": "İlk",
            "last": "Axırıncı",
            "next": "Sonrakı",
            "previous": "Öncəki"
        }
    };

    const tables = {
        columnsTable: $('#columnsTable').DataTable({
            language: dataTablesAz
        }),
        schoolsTable: $('#schoolsTable').DataTable({
            language: dataTablesAz
        }),
        adminsTable: $('#adminsTable').DataTable({
            language: dataTablesAz
        })
    };

    // Alert funksiyası
    function showAlert(type, message, container = '#mainAlert') {
        const alertDiv = $(`<div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>`);
        
        $(container).html(alertDiv);
        
        // 5 saniyə sonra alert-i gizlət
        setTimeout(() => {
            alertDiv.alert('close');
        }, 5000);
    }

    // Modal açılanda formu sıfırla
    $('#columnModal').on('show.bs.modal', function(event) {
        const modal = $(this);
        const button = $(event.relatedTarget); // Button that triggered the modal
        const form = modal.find('form');
        
        // If button exists, it's an add operation. If not, it's an edit operation
        if (button.length) {
            form.trigger('reset');
            form.find('input[name="id"]').val('');
            modal.find('.modal-title').text('Sütun Əlavə Et');
        } else {
            modal.find('.modal-title').text('Sütunu Düzəlt');
        }
        
        modal.find('#columnModalAlert').empty();
    });

    // Sütun düzəltmə
    $('.edit-column').on('click', function() {
        const id = $(this).data('id');
        const row = $(this).closest('tr');
        
        // Sütun məlumatlarını al
        const columnData = {
            name: row.find('td:nth-child(1)').text().trim(),
            type: row.find('td:nth-child(2)').text().trim().toLowerCase(),
            deadline: row.find('td:nth-child(3)').text().trim(),
            isActive: row.find('.badge').hasClass('bg-success')
        };

        // Modal-ı doldur
        const modal = $('#columnModal');
        const form = modal.find('form');
        
        // ID və ad
        form.find('input[name="id"]').val(id);
        form.find('input[name="name"]').val(columnData.name);
        
        // Tip
        form.find('select[name="type"]').val(columnData.type);
        
        // Deadline formatını düzəlt
        if (columnData.deadline && columnData.deadline !== '-') {
            try {
                const date = new Date(columnData.deadline);
                if (!isNaN(date.getTime())) {
                    const formattedDate = date.toISOString().slice(0, 16);
                    form.find('input[name="deadline"]').val(formattedDate);
                }
            } catch (e) {
                console.error('Error parsing date:', e);
                form.find('input[name="deadline"]').val('');
            }
        } else {
            form.find('input[name="deadline"]').val('');
        }
        
        // Aktiv/Passiv
        form.find('input[name="is_active"]').prop('checked', columnData.isActive);
        
        modal.modal('show');
    });

    // Sütun silmə
    $('.delete-column').on('click', function(e) {
        e.preventDefault();
        
        const id = $(this).data('id');
        const btn = $(this);
        const columnName = btn.closest('tr').find('td:eq(0)').text().trim();

        if (confirm(`"${columnName}" sütununu silmək istədiyinizdən əminsiniz?`)) {
            $.ajax({
                url: '/settings/deleteColumn',
                method: 'POST',
                data: { id: id },
                beforeSend: function() {
                    btn.prop('disabled', true);
                },
                success: function(response) {
                    if (response.success) {
                        btn.closest('tr').remove();
                        showAlert('success', 'Sütun uğurla silindi');
                    } else {
                        showAlert('error', response.error || 'Xəta baş verdi');
                        btn.prop('disabled', false);
                    }
                },
                error: function(xhr) {
                    showAlert('error', 'Server xətası: ' + (xhr.responseJSON?.error || 'Bilinməyən xəta'));
                    btn.prop('disabled', false);
                }
            });
        }
    });

    // Sütun formu göndərilməsi
    $('#columnForm').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const submitBtn = form.find('button[type="submit"]');
        const id = form.find('input[name="id"]').val();
        const url = id ? '/settings/updateColumn' : '/settings/addColumn';
        
        // Form məlumatlarını yoxla
        const name = form.find('input[name="name"]').val().trim();
        const type = form.find('select[name="type"]').val();
        
        if (!name || !type) {
            showAlert('error', 'Ad və tip sahələri məcburidir', '#columnModalAlert');
            return;
        }

        $.ajax({
            url: url,
            method: 'POST',
            data: form.serialize(),
            beforeSend: function() {
                submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Gözləyin...');
            },
            success: function(response) {
                if (response.success) {
                    $('#columnModal').modal('hide');
                    showAlert('success', response.message || (id ? 'Sütun uğurla yeniləndi' : 'Sütun uğurla əlavə edildi'));
                    // Sütunlar cədvəlini yenilə
                    refreshTable('#columnsTable');
                    // Formu təmizlə
                    form[0].reset();
                } else {
                    showAlert('error', response.error || 'Xəta baş verdi', '#columnModalAlert');
                    submitBtn.prop('disabled', false).text('Yadda Saxla');
                }
            },
            error: function(xhr) {
                let errorMessage = 'Server xətası baş verdi';
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.error) {
                        errorMessage = response.error;
                    }
                } catch (e) {
                    console.error('Error parsing response:', e);
                }
                showAlert('error', errorMessage, '#columnModalAlert');
                submitBtn.prop('disabled', false).text('Yadda Saxla');
            }
        });
    });

    // Məktəb redaktə etmə
    $('.edit-school').on('click', function() {
        const id = $(this).data('id');
        const row = $(this).closest('tr');
        const name = row.find('td:eq(0)').text();

        // Modal-ı doldur
        $('#schoolModal').find('input[name="id"]').val(id);
        $('#schoolModal').find('input[name="name"]').val(name);
        
        // Modal başlığını yenilə
        $('#schoolModal .modal-title').text('Məktəbi Düzəlt');
        $('#schoolModal').modal('show');
    });

    // Məktəb silmə
    $('.delete-school').on('click', function() {
        if (!confirm('Bu məktəbi silmək istədiyinizdən əminsiniz? Məktəbə aid adminlər varsa, əvvəlcə onları silməlisiniz.')) {
            return;
        }

        const id = $(this).data('id');
        const btn = $(this);

        $.ajax({
            url: '/settings/deleteSchool',
            method: 'POST',
            data: { id: id },
            success: function(response) {
                if (response.success) {
                    // Cədvəldən sətri sil
                    btn.closest('tr').remove();
                    showAlert('success', 'Məktəb uğurla silindi');
                } else {
                    showAlert('error', response.error || 'Xəta baş verdi');
                }
            },
            error: function() {
                showAlert('error', 'Server xətası');
            }
        });
    });

    // Məktəb formu göndərilməsi
    $('#schoolForm').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const submitBtn = form.find('button[type="submit"]');
        const id = form.find('input[name="id"]').val();
        const url = id ? '/settings/updateSchool' : '/settings/addSchool';

        // Məktəb adını yoxla
        const name = form.find('input[name="name"]').val().trim();
        if (!name) {
            showAlert('error', 'Məktəb adı daxil edilməlidir', '#schoolModalAlert');
            return;
        }

        $.ajax({
            url: url,
            method: 'POST',
            data: form.serialize(),
            contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
            dataType: 'json',
            beforeSend: function() {
                submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Gözləyin...');
            },
            success: function(response) {
                if (response.success) {
                    $('#schoolModal').modal('hide');
                    showAlert('success', response.message || 'Məktəb uğurla əlavə edildi');
                    // Məktəblər cədvəlini yenilə
                    refreshTable('#schoolsTable');
                    // Formu təmizlə
                    form[0].reset();
                } else {
                    showAlert('error', response.error || 'Xəta baş verdi', '#schoolModalAlert');
                }
                submitBtn.prop('disabled', false).text('Yadda Saxla');
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                console.error('Response:', xhr.responseText);
                
                let errorMessage = 'Server xətası baş verdi';
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.error) {
                        errorMessage = response.error;
                    }
                } catch (e) {
                    console.error('Error parsing response:', e);
                }
                
                showAlert('error', errorMessage, '#schoolModalAlert');
                submitBtn.prop('disabled', false).text('Yadda Saxla');
            }
        });
    });

    // Admin redaktə etmə
    $('.edit-admin').on('click', function() {
        const id = $(this).data('id');
        const row = $(this).closest('tr');
        const name = row.find('td:eq(0)').text().trim();
        const username = row.find('td:eq(1)').text().trim();
        const schoolName = row.find('td:eq(2)').text().trim();
        const isActive = row.find('.badge').hasClass('bg-success');
        
        // School ID-ni əldə et
        const schoolId = row.find('td:eq(2)').data('school-id');

        // Modal-ı doldur
        $('#adminModal').find('input[name="id"]').val(id);
        $('#adminModal').find('input[name="name"]').val(name);
        $('#adminModal').find('input[name="username"]').val(username);
        $('#adminModal').find('select[name="school_id"]').val(schoolId);
        $('#adminModal').find('input[name="is_active"]').prop('checked', isActive);
        $('#adminModal').find('input[name="password"]').val(''); // Şifrə sahəsini təmizlə
        
        // Modal başlığını yenilə
        $('#adminModal .modal-title').text('Məktəb Adminini Düzəlt');
        $('#adminModal').modal('show');
    });

    // Admin silmə
    $('.delete-admin').on('click', function(e) {
        e.preventDefault(); // Click hadisəsini dayandır
        
        const id = $(this).data('id');
        const btn = $(this);
        const adminName = btn.closest('tr').find('td:eq(0)').text().trim();

        if (confirm(`${adminName} adlı məktəb adminini silmək istədiyinizdən əminsiniz?`)) {
            $.ajax({
                url: '/settings/deleteSchoolAdmin', // URL düzəldildi
                method: 'POST',
                data: { id: id },
                beforeSend: function() {
                    btn.prop('disabled', true); // Düyməni deaktiv et
                },
                success: function(response) {
                    if (response.success) {
                        // Cədvəldən sətri sil
                        btn.closest('tr').remove();
                        showAlert('success', 'Məktəb admini uğurla silindi');
                    } else {
                        showAlert('error', response.error || 'Xəta baş verdi');
                        btn.prop('disabled', false);
                    }
                },
                error: function(xhr) {
                    showAlert('error', 'Server xətası: ' + (xhr.responseJSON?.error || 'Bilinməyən xəta'));
                    btn.prop('disabled', false);
                }
            });
        }
    });

    // Admin formu göndərilməsi
    $('#adminForm').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const submitBtn = form.find('button[type="submit"]');
        const id = form.find('input[name="id"]').val();
        const url = id ? '/settings/updateSchoolAdmin' : '/settings/addSchoolAdmin';

        $.ajax({
            url: url,
            method: 'POST',
            data: form.serialize(),
            contentType: 'application/x-www-form-urlencoded; charset=UTF-8',
            dataType: 'json',
            beforeSend: function() {
                submitBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Gözləyin...');
            },
            success: function(response) {
                if (response.success) {
                    $('#adminModal').modal('hide');
                    showAlert('success', response.message || (id ? 'Admin uğurla yeniləndi' : 'Admin uğurla əlavə edildi'));
                    // Adminlər cədvəlini yenilə
                    refreshTable('#adminsTable');
                    // Formu təmizlə
                    form[0].reset();
                } else {
                    showAlert('error', response.error || 'Xəta baş verdi', '#adminModalAlert');
                }
                submitBtn.prop('disabled', false).text('Yadda Saxla');
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                console.error('Response:', xhr.responseText);
                
                let errorMessage = 'Server xətası baş verdi';
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.error) {
                        errorMessage = response.error;
                    }
                } catch (e) {
                    console.error('Error parsing response:', e);
                }
                
                showAlert('error', errorMessage, '#adminModalAlert');
                submitBtn.prop('disabled', false).text('Yadda Saxla');
            }
        });
    });

    // Delete events
    $('.delete-column, .delete-school').click(function() {
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

    // Import Schools
    $('#importSchoolsForm').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const btn = form.find('button[type="submit"]');
        const formData = new FormData(this);
        
        btn.prop('disabled', true)
           .html('<i class="fas fa-spinner fa-spin"></i> İmport edilir...');
        
        $.ajax({
            url: '/settings/importSchools',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                const data = typeof response === 'string' ? JSON.parse(response) : response;
                if (data.success) {
                    showNotification(data.message, 'success');
                    if (data.errors && data.errors.length > 0) {
                        data.errors.forEach(error => {
                            showNotification(error, 'warning');
                        });
                    }
                    form[0].reset();
                } else {
                    showNotification(data.error || 'İmport zamanı xəta baş verdi', 'danger');
                }
            },
            error: function(xhr) {
                let errorMessage = 'İmport zamanı xəta baş verdi';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                }
                showNotification(errorMessage, 'danger');
            },
            complete: function() {
                btn.prop('disabled', false)
                   .html('<i class="fas fa-file-import"></i> İmport Et');
            }
        });
    });
    
    // Import School Admins
    $('#importAdminsForm').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const btn = form.find('button[type="submit"]');
        const formData = new FormData(this);
        
        btn.prop('disabled', true)
           .html('<i class="fas fa-spinner fa-spin"></i> İmport edilir...');
        
        $.ajax({
            url: '/settings/importSchoolAdmins',
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                const data = typeof response === 'string' ? JSON.parse(response) : response;
                if (data.success) {
                    showNotification(data.message, 'success');
                    if (data.errors && data.errors.length > 0) {
                        data.errors.forEach(error => {
                            showNotification(error, 'warning');
                        });
                    }
                    form[0].reset();
                } else {
                    showNotification(data.error || 'İmport zamanı xəta baş verdi', 'danger');
                }
            },
            error: function(xhr) {
                let errorMessage = 'İmport zamanı xəta baş verdi';
                if (xhr.responseJSON && xhr.responseJSON.error) {
                    errorMessage = xhr.responseJSON.error;
                }
                showNotification(errorMessage, 'danger');
            },
            complete: function() {
                btn.prop('disabled', false)
                   .html('<i class="fas fa-file-import"></i> İmport Et');
            }
        });
    });

    // Cədvəlləri yeniləmək üçün funksiya
    function refreshTable(tableId) {
        $.ajax({
            url: '/settings',
            method: 'GET',
            success: function(response) {
                // Əvvəlcə mövcud DataTable-ı destroy et
                if ($.fn.DataTable.isDataTable(tableId)) {
                    $(tableId).DataTable().destroy();
                }
                
                // HTML-dən cədvəli tap və yenilə
                const newTable = $(response).find(tableId + ' tbody').html();
                $(tableId + ' tbody').html(newTable);
                
                // DataTable-ı yenidən initialize et
                $(tableId).DataTable({
                    language: dataTablesAz
                });
            }
        });
    }
});
</script>