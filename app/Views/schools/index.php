<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col">
            <h2>Məktəblər</h2>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="schoolsTable">
                    <thead>
                        <tr>
                            <th>Məktəb Adı</th>
                            <th>Admin İstifadəçi Adı</th>
                            <th>Son Giriş</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($schools as $school): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($school['name']); ?></td>
                            <td><?php echo htmlspecialchars($school['admin_username'] ?? '-'); ?></td>
                            <td><?php echo $school['last_login'] ? date('d.m.Y H:i', strtotime($school['last_login'])) : '-'; ?></td>
                            <td>
                                <span class="badge bg-<?php echo $school['is_active'] ? 'success' : 'danger'; ?>">
                                    <?php echo $school['is_active'] ? 'Aktiv' : 'Deaktiv'; ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>