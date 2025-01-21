<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col">
            <h2>Dashboard</h2>
        </div>
        <div class="col text-end">
            <button type="button" class="btn btn-success" id="exportExcel">
                <i class="fas fa-file-excel"></i> Excel Export
            </button>
        </div>
    </div>

    <!-- Data Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable">
                    <thead>
                        <tr>
                            <th>Məktəb</th>
                            <?php foreach($columns as $column): ?>
                            <th><?php echo htmlspecialchars($column['name']); ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($schools as $school): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($school['name']); ?></td>
                            <?php foreach($columns as $column): ?>
                            <td class="data-cell" 
                                data-school="<?php echo $school['id']; ?>" 
                                data-column="<?php echo $column['id']; ?>">
                                <?php 
                                $value = array_filter($data, function($d) use ($school, $column) {
                                    return $d['school_id'] == $school['id'] && $d['column_id'] == $column['id'];
                                });
                                echo !empty($value) ? htmlspecialchars(current($value)['value']) : '-';
                                ?>
                            </td>
                            <?php endforeach; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>