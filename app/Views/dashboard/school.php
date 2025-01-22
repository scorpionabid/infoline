<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<!-- Add school ID for JavaScript -->
<meta name="school-id" content="<?php echo $_SESSION['school_id']; ?>">

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Məktəb Məlumatları</h1>
        <div class="d-flex gap-2">
            <button class="btn btn-primary" id="saveChanges" style="display: none;">
                <i class="fas fa-save"></i> Yadda Saxla
            </button>
            <button class="btn btn-success" id="excelExport">
                <i class="fas fa-file-excel"></i> Excel Export
            </button>
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
                        <?php
                        $currentValue = '';
                        foreach ($data as $item) {
                            if ($item['column_id'] == $column['id']) {
                                $currentValue = $item['value'];
                                break;
                            }
                        }
                        ?>
                        <div class="data-cell" 
                             data-column="<?php echo $column['id']; ?>"
                             contenteditable="true"><?php echo htmlspecialchars($currentValue); ?></div>
                    </td>
                    <td>
                        <?php
                        $updated = '-';
                        foreach ($data as $item) {
                            if ($item['column_id'] == $column['id']) {
                                $updated = date('d.m.Y H:i', strtotime($item['updated_at']));
                                break;
                            }
                        }
                        echo $updated;
                        ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
.data-cell {
    min-height: 24px;
    padding: 6px;
    border: 1px solid transparent;
}

.data-cell:hover {
    border-color: #dee2e6;
    background-color: #f8f9fa;
    cursor: text;
}

.data-cell:focus {
    outline: 2px solid #007bff;
    outline-offset: -2px;
    background-color: #fff;
}
</style>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>