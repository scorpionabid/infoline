<!-- app/Views/profile/index.php -->
<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Profil Məlumatları</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <th style="width: 150px;">Ad:</th>
                                    <td><?php echo htmlspecialchars($user['name'] ?? ''); ?></td>
                                </tr>
                                <tr>
                                    <th>İstifadəçi adı:</th>
                                    <td><?php echo htmlspecialchars($user['username'] ?? ''); ?></td>
                                </tr>
                                <?php if (isset($user['email'])): ?>
                                <tr>
                                    <th>Email:</th>
                                    <td><?php echo htmlspecialchars($user['email'] ?? ''); ?></td>
                                </tr>
                                <?php endif; ?>
                                <tr>
                                    <th>Rol:</th>
                                    <td>
                                        <?php 
                                        $roles = [
                                            'super_admin' => 'Super Admin',
                                            'school_admin' => 'Məktəb Admini'
                                        ];
                                        echo htmlspecialchars($roles[$user['role']] ?? $user['role']); 
                                        ?>
                                    </td>
                                </tr>
                                <?php if ($user['role'] === 'school_admin' && isset($user['school_id'])): ?>
                                <tr>
                                    <th>Məktəb ID:</th>
                                    <td><?php echo htmlspecialchars($user['school_id']); ?></td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>