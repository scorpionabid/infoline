<div class="container">
    <div class="row mb-4">
        <div class="col">
            <h2>Sütunlar</h2>
        </div>
        <div class="col text-end">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addColumnModal">
                Yeni Sütun
            </button>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
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
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-danger delete-column" data-id="<?php echo $column['id']; ?>">
                                    <i class="bi bi-trash"></i>
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

<!-- Add Column Modal -->
<div class="modal fade" id="addColumnModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Yeni Sütun</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addColumnForm">
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
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Son Tarix</label>
                        <input type="datetime-local" class="form-control" name="deadline">
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" name="is_active" checked>
                            <label class="form-check-label">Aktiv</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Ləğv et</button>
                <button type="button" class="btn btn-primary" id="saveColumn">Yadda saxla</button>
            </div>
        </div>
    </div>
</div>