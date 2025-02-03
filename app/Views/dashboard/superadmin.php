<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="container-fluid py-4">
    <!-- Kateqoriyalar -->
    <div class="categories-nav mb-4">
        <div class="d-flex">
            <?php 
            $defaultCategory = null;
            foreach ($categories as $category): 
                if ($category['name'] === 'Digər') {
                    $defaultCategory = $category;
                }
            ?>
                <div class="category-item">
                    <a class="btn btn-outline-primary category-link me-2 <?php echo $category['name'] === 'Digər' ? 'active' : ''; ?>" 
                       href="#" 
                       data-category="<?php echo htmlspecialchars($category['id']); ?>">
                        <?php echo htmlspecialchars($category['name']); ?>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3" id="currentCategory">
            <?php echo $defaultCategory ? htmlspecialchars($defaultCategory['name']) : 'Məktəb Məlumatları'; ?>
        </h1>
        <button class="btn btn-success" id="excelExport">
            <i class="fas fa-file-excel"></i> Excel Export
        </button>
    </div>

    <?php if (isset($error) && $error): ?>
        <div class="alert alert-danger">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <div class="loading-overlay" style="display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                <table class="table table-bordered table-hover" id="dataTable">
                    <thead class="table-light">
                        <tr id="headerRow">
                            <th scope="col" class="text-center sticky-column">Məktəb</th>
                            <?php foreach ($columns['data'] as $column): ?>
                                <th scope="col" class="text-center">
                                    <?php echo htmlspecialchars($column['name']); ?>
                                </th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        <?php foreach ($schools as $school): ?>
                            <tr>
                                <th scope="row" class="font-weight-bold sticky-column" data-school-id="<?php echo $school['id']; ?>">
                                    <?php echo htmlspecialchars($school['name']); ?>
                                </th>
                                <?php foreach ($columns['data'] as $column): ?>
                                    <?php 
                                        $value = '';
                                        foreach ($data as $item) {
                                            if ($item['school_id'] == $school['id'] && $item['column_id'] == $column['id']) {
                                                $value = $item['value'];
                                                break;
                                            }
                                        }
                                    ?>
                                    <td class="data-cell" 
                                        data-school="<?php echo $school['id']; ?>" 
                                        data-column="<?php echo $column['id']; ?>"
                                        contenteditable="true">
                                        <?php echo htmlspecialchars($value); ?>
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

<style>
.categories-nav {
    overflow-x: auto;
    padding: 10px 0;
}

.category-link {
    white-space: nowrap;
}

.category-link.active {
    background-color: #0d6efd;
    color: white;
}

.table th {
    background-color: #f8f9fa;
}

.sticky-column {
    position: sticky;
    left: 0;
    background-color: #f8f9fa;
    z-index: 1;
}

.table-responsive {
    position: relative;
    min-height: 200px;
}

.loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255, 255, 255, 0.8);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.data-cell {
    background-color: white;
}
</style>

<script>
$(document).ready(function() {
    function showLoading() {
        $('.loading-overlay').show();
    }

    function hideLoading() {
        $('.loading-overlay').hide();
    }

    function updateTable(data) {
        const headerRow = $('#headerRow');
        const tableBody = $('#tableBody');
        
        // Clear existing columns except school name
        headerRow.find('th:not(:first)').remove();
        tableBody.find('tr').each(function() {
            $(this).find('td').remove();
        });

        // Add column headers
        data.columns.forEach(column => {
            headerRow.append(`
                <th scope="col" class="text-center">
                    ${column.name}
                </th>
            `);
        });

        // Add data cells
        tableBody.find('tr').each(function() {
            const schoolId = $(this).find('th').data('school-id');
            
            data.columns.forEach(column => {
                const value = data.data.find(item => 
                    item.school_id == schoolId && 
                    item.column_id == column.id
                )?.value || '';

                $(this).append(`
                    <td class="data-cell" 
                        data-school="${schoolId}" 
                        data-column="${column.id}"
                        contenteditable="true">
                        ${value}
                    </td>
                `);
            });
        });
    }

    // Category click handler
    $('.category-link').click(function(e) {
        e.preventDefault();
        
        $('.category-link').removeClass('active');
        $(this).addClass('active');
        
        const categoryId = $(this).data('category');
        const categoryName = $(this).text().trim();
        
        $('#currentCategory').text(categoryName);
        
        showLoading();
        
        $.ajax({
            url: '/dashboard/getData',
            method: 'GET',
            data: { category: categoryId },
            success: function(response) {
                if (response.success) {
                    updateTable(response);
                } else {
                    alert('Məlumatları yükləmək mümkün olmadı');
                }
            },
            error: function() {
                alert('Sistem xətası baş verdi');
            },
            complete: function() {
                hideLoading();
            }
        });
    });

    // Initialize with Digər category
    if ($('.category-link.active').length) {
        $('.category-link.active').click();
    } else {
        $('.category-link:first').click();
    }
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>