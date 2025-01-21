<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col">
            <h2>Məktəb Məlumatları</h2>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Məlumat növü</th>
                    <th>Dəyər</th>
                    <th>Son yenilənmə</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($columns as $column): ?>
                <tr>
                    <td><?php echo htmlspecialchars($column['name']); ?></td>
                    <td>
                        <input type="<?php echo $column['type'] === 'number' ? 'number' : 'text'; ?>" 
                               class="form-control data-input"
                               data-column="<?php echo $column['id']; ?>"
                               value="<?php 
                               $value = array_filter($data, function($d) use ($column) {
                                   return $d['column_id'] == $column['id'];
                               });
                               echo !empty($value) ? htmlspecialchars(current($value)['value']) : '';
                               ?>">
                    </td>
                    <td>
                        <?php
                        if (!empty($value)) {
                            $updated = current($value)['updated_at'];
                            echo date('d.m.Y H:i', strtotime($updated));
                        } else {
                            echo '-';
                        }
                        ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>