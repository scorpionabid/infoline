<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Məktəblər</h1>
        <button class="btn btn-primary" id="addSchool">
            <i class="fas fa-plus"></i> Yeni Məktəb
        </button>
    </div>

    <div class="row">
        <!-- Ümumi məktəb sayı -->
        <div class="col-md-3 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2">ÜMUMI MƏKTƏB SAYI</h6>
                            <h4 class="mb-0"><?php echo count($schools); ?></h4>
                        </div>
                        <div class="text-primary">
                            <i class="fas fa-school fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Aktiv məktəblər -->
        <div class="col-md-3 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2">AKTIV MƏKTƏBLƏR</h6>
                            <h4 class="mb-0"><?php echo count($schools); ?></h4>
                        </div>
                        <div class="text-success">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Deaktiv məktəblər -->
        <div class="col-md-3 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2">DEAKTIV MƏKTƏBLƏR</h6>
                            <h4 class="mb-0">0</h4>
                        </div>
                        <div class="text-danger">
                            <i class="fas fa-times-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
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
                        <?php foreach ($schools as $school): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($school['name']); ?></td>
                            <td><?php echo isset($school['admin_count']) ? $school['admin_count'] : 0; ?></td>
                            <td>
                                <button class="btn btn-sm btn-primary editSchool" data-id="<?php echo $school['id']; ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger deleteSchool" data-id="<?php echo $school['id']; ?>">
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

<!-- Add/Edit School Modal -->
<div class="modal fade" id="schoolModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Məktəb Əlavə Et/Düzəlt</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="schoolForm">
                    <input type="hidden" id="schoolId">
                    <div class="mb-3">
                        <label for="schoolName" class="form-label">Məktəb Adı</label>
                        <input type="text" class="form-control" id="schoolName" required>
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

<script>
$(document).ready(function() {
    const schoolModal = new bootstrap.Modal(document.getElementById('schoolModal'));
    
    // DataTable
    $('#schoolsTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/az.json'
        }
    });

    // Add School
    $('#addSchool').click(function() {
        $('#schoolId').val('');
        $('#schoolName').val('');
        $('#schoolModal .modal-title').text('Məktəb Əlavə Et');
        schoolModal.show();
    });

    // Edit School
    $('.editSchool').click(function() {
        const id = $(this).data('id');
        const name = $(this).closest('tr').find('td:first').text();
        
        $('#schoolId').val(id);
        $('#schoolName').val(name);
        $('#schoolModal .modal-title').text('Məktəb Düzəlt');
        schoolModal.show();
    });

    // Save School
    $('#saveSchool').click(function() {
        const id = $('#schoolId').val();
        const name = $('#schoolName').val();

        if (!name) {
            alert('Məktəb adı daxil edin');
            return;
        }

        $.post('/schools/' + (id ? 'update' : 'create'), {
            id: id,
            name: name
        }, function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert(response.error || 'Xəta baş verdi');
            }
        });
    });

    // Delete School
    $('.deleteSchool').click(function() {
        if (!confirm('Məktəbi silmək istədiyinizə əminsiniz?')) {
            return;
        }

        const id = $(this).data('id');
        $.post('/schools/delete', { id: id }, function(response) {
            if (response.success) {
                location.reload();
            } else {
                alert(response.error || 'Xəta baş verdi');
            }
        });
    });
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>