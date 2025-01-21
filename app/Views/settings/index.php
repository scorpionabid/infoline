<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col">
            <h2>Ayarlar</h2>
        </div>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#columns">
                <i class="fas fa-table"></i> Sütunlar
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#schools">
                <i class="fas fa-school"></i> Məktəblər və Adminlər
            </button>
        </li>
    </ul>

    <!-- Tab Contents -->
    <div class="tab-content">
        <!-- Columns Tab -->
        <div class="tab-pane fade show active" id="columns">
            <div class="mb-3">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addColumnModal">
                    <i class="fas fa-plus"></i> Yeni Sütun
                </button>
            </div>
            <div class="card">
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
                                        <div class="form-check form-switch">
                                            <input class="form-check-input toggle-column" type="checkbox" 
                                                   data-id="<?php echo $column['id']; ?>"
                                                   <?php echo $column['is_active'] ? 'checked' : ''; ?>>
                                        </div>
                                    </td>
                                    <td>
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

        <!-- Schools Tab -->
        <div class="tab-pane fade" id="schools">
            <div class="mb-3">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSchoolAdminModal">
                    <i class="fas fa-plus"></i> Yeni Məktəb və Admin
                </button>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="schoolAdminsTable">
                            <thead>
                                <tr>
                                    <th>Məktəb Adı</th>
                                    <th>Admin İstifadəçi Adı</th>
                                    <th>Status</th>
                                    <th>Əməliyyatlar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($schools as $school): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($school['name']); ?></td>
                                    <td><?php echo htmlspecialchars($school['admin_username'] ?? '-'); ?></td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input toggle-school" type="checkbox" 
                                                   data-id="<?php echo $school['id']; ?>"
                                                   <?php echo $school['is_active'] ? 'checked' : ''; ?>>
                                        </div>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-warning reset-password" data-id="<?php echo $school['id']; ?>">
                                            <i class="fas fa-key"></i>
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
    </div>
</div>

<!-- Add Column Modal -->
<div class="modal fade" id="addColumnModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Yeni Sütun Əlavə Et</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addColumnForm">
                    <div class="mb-3">
                        <label for="columnName" class="form-label">Sütun adı</label>
                        <input type="text" class="form-control" id="columnName" required>
                    </div>
                    <div class="mb-3">
                        <label for="columnType" class="form-label">Məlumat tipi</label>
                        <select class="form-control" id="columnType" required>
                            <option value="text">Mətn</option>
                            <option value="number">Rəqəm</option>
                            <option value="date">Tarix</option>
                            <option value="select">Seçim</option>
                        </select>
                    </div>
                    <div class="mb-3" id="optionsDiv" style="display: none;">
                        <label for="options" class="form-label">Seçimlər (hər sətirdə bir seçim)</label>
                        <textarea class="form-control" id="options" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="deadline" class="form-label">Son tarix</label>
                        <input type="datetime-local" class="form-control" id="deadline">
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="isRequired">
                            <label class="form-check-label" for="isRequired">
                                Məcburidir
                            </label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bağla</button>
                <button type="button" class="btn btn-primary" id="saveColumn">Yadda saxla</button>
            </div>
        </div>
    </div>
</div>

<!-- Add School Admin Modal -->
<div class="modal fade" id="addSchoolAdminModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Yeni Məktəb Admini Əlavə Et</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addSchoolAdminForm">
                    <div class="mb-3">
                        <label for="schoolName" class="form-label">Məktəb Adı</label>
                        <input type="text" class="form-control" id="schoolName" required>
                    </div>
                    <div class="mb-3">
                        <label for="adminUsername" class="form-label">İstifadəçi adı</label>
                        <input type="text" class="form-control" id="adminUsername" required>
                    </div>
                    <div class="mb-3">
                        <label for="adminPassword" class="form-label">Şifrə</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="adminPassword" required>
                            <button class="btn btn-outline-secondary" type="button" id="generatePassword">
                                <i class="fas fa-dice"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="adminEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="adminEmail">
                    </div>
                    <div class="mb-3">
                        <label for="adminPhone" class="form-label">Telefon</label>
                        <input type="tel" class="form-control" id="adminPhone">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bağla</button>
                <button type="button" class="btn btn-primary" id="saveSchoolAdmin">Yadda saxla</button>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>