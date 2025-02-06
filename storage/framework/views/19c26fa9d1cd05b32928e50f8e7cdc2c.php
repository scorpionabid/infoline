<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Məktəb Paneli</h1>
        <div class="d-flex gap-2">
            <button class="btn btn-success" id="saveChanges" style="display: none;">
                <i class="fas fa-save"></i> Yadda Saxla
            </button>
            <button class="btn btn-primary" id="exportExcel">
                <i class="fas fa-file-excel"></i> Excel Export
            </button>
        </div>
    </div>

    <!-- Kateqoriya seçimi -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="btn-group" role="group">
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <button type="button" 
                            class="btn btn-outline-primary category-btn" 
                            data-category-id="<?php echo e($category->id); ?>">
                        <?php echo e($category->name); ?>

                    </button>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>

    <!-- Data cədvəli -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable">
                    <thead class="table-light">
                        <tr>
                            <th>Sütun adı</th>
                            <th>Dəyər</th>
                            <th>Son Dəyişilmə</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- JavaScript ilə doldurulacaq -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    let activeCategory = null;
    const schoolId = <?php echo e(auth()->user()->school_id); ?>;
    let hasChanges = false;

    // Kateqoriya düyməsinə klik
    $('.category-btn').on('click', function() {
        const categoryId = $(this).data('category-id');
        $('.category-btn').removeClass('active');
        $(this).addClass('active');
        loadCategoryData(categoryId);
    });

    // Kateqoriya məlumatlarını yükləmək
    function loadCategoryData(categoryId) {
        $.get(`/api/v1/data-values?category_id=${categoryId}&school_id=${schoolId}`, function(response) {
            if (response.success) {
                renderTable(response.data);
            }
        });
    }

    // Cədvəli render etmək
    function renderTable(data) {
        const tbody = $('#dataTable tbody');
        tbody.empty();

        data.forEach(item => {
            tbody.append(`
                <tr>
                    <td>${item.column_name}</td>
                    <td>
                        <div class="data-cell" 
                             data-column-id="${item.column_id}" 
                             contenteditable="true">
                            ${item.value || ''}
                        </div>
                    </td>
                    <td>${item.updated_at || '-'}</td>
                    <td>
                        <span class="badge bg-${getStatusBadge(item.status)}">
                            ${getStatusText(item.status)}
                        </span>
                    </td>
                </tr>
            `);
        });
    }

    // Status badge rəngi
    function getStatusBadge(status) {
        switch(status) {
            case 'draft': return 'warning';
            case 'submitted': return 'info';
            case 'approved': return 'success';
            case 'rejected': return 'danger';
            default: return 'secondary';
        }
    }

    // Status mətn
    function getStatusText(status) {
        switch(status) {
            case 'draft': return 'Qaralama';
            case 'submitted': return 'Göndərilib';
            case 'approved': return 'Təsdiqlənib';
            case 'rejected': return 'Rədd edilib';
            default: return 'Naməlum';
        }
    }

    // Data dəyişdikdə
    $(document).on('input', '.data-cell', function() {
        hasChanges = true;
        $('#saveChanges').show();
    });

    // Yadda saxla düyməsi
    $('#saveChanges').on('click', function() {
        const updates = [];
        $('.data-cell').each(function() {
            updates.push({
                column_id: $(this).data('column-id'),
                value: $(this).text().trim()
            });
        });

        $.ajax({
            url: '/api/v1/data-values/bulk-update',
            method: 'POST',
            data: JSON.stringify({
                school_id: schoolId,
                updates: updates
            }),
            contentType: 'application/json',
            success: function(response) {
                if (response.success) {
                    hasChanges = false;
                    $('#saveChanges').hide();
                    alert('Məlumatlar yadda saxlanıldı');
                }
            }
        });
    });

    // Excel export
    $('#exportExcel').on('click', function() {
        window.location.href = `/api/v1/export/excel?school_id=${schoolId}`;
    });

    // İlk kateqoriyanı seç
    $('.category-btn:first').click();
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/home/Library/CloudStorage/OneDrive-BureauonICTforEducation,MinistryofEducation/infoline_app/resources/views/pages/dashboard/school-admin.blade.php ENDPATH**/ ?>