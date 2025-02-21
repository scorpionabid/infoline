<!-- Sütun Əlavə Et Modal -->
<div class="modal fade" id="addColumnModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Yeni Sütun</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addColumnForm" method="POST" action="/settings/table/column" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="category_id" value="{{ request()->query('category') }}">
                <div class="modal-body">
                    <div class="alert alert-danger form-errors" style="display: none;"></div>
                    <div class="row">
                        <!-- Sütun adı -->
                        <div class="col-md-6 mb-3">
                            <label for="columnName" class="form-label">Sütun adı</label>
                            <input type="text" class="form-control" id="columnName" name="name" required 
                                   minlength="2" maxlength="255" pattern="[a-zA-Z0-9\s]+" title="Sütun adı yalnız hərflər, rəqəmlər və boşluqlardan ibarət ola bilər">
                        </div>

                        <!-- Sütun tipi -->
                        <div class="col-md-6 mb-3">
                            <label for="columnType" class="form-label">Tip</label>
                            <select class="form-select" id="columnType" name="data_type" required>
                                <option value="">Seçin...</option>
                                <option value="text">Mətn</option>
                                <option value="number">Rəqəm</option>
                                <option value="date">Tarix</option>
                                <option value="select">Seçim</option>
                                <option value="file">Fayl</option>
                            </select>
                        </div>

                        <!-- Sütun təsviri -->
                        <div class="col-12 mb-3">
                            <label for="columnDescription" class="form-label">Təsvir</label>
                            <textarea class="form-control" id="columnDescription" name="description" 
                                      rows="2" maxlength="1000"></textarea>
                        </div>

                        <!-- Seçimlər (select tipi üçün) -->
                        <div class="col-12 mb-3" id="optionsWrapper" style="display: none;">
                            <label class="form-label">Seçimlər</label>
                            <div id="optionsList">
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control" name="options[]" 
                                           placeholder="Seçim daxil edin" maxlength="255" pattern="[a-zA-Z0-9\s]+" title="Seçim yalnız hərflər, rəqəmlər və boşluqlardan ibarət ola bilər">
                                    <button type="button" class="btn btn-outline-danger" 
                                            onclick="$(this).closest('.input-group').remove()">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="addOption()">
                                <i class="fas fa-plus me-1"></i> Yeni seçim
                            </button>
                        </div>

                        <!-- Məcburilik -->
                        <div class="col-md-6 mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="columnRequired" 
                                       name="is_required" value="1">
                                <label class="form-check-label" for="columnRequired">Məcburidir</label>
                            </div>
                        </div>

                        <!-- Son tarix -->
                        <div class="col-md-6 mb-3">
                            <label for="columnEndDate" class="form-label">Son tarix</label>
                            <input type="date" class="form-control" id="columnEndDate" name="end_date">
                        </div>

                        <!-- Limit -->
                        <div class="col-md-6 mb-3">
                            <label for="columnLimit" class="form-label">Daxiletmə limiti</label>
                            <input type="number" class="form-control" id="columnLimit" name="input_limit" 
                                   min="0" value="0">
                            <small class="text-muted">0 = limitsiz</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Ləğv et</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> Yadda saxla
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function addOption() {
    const optionHtml = `
        <div class="input-group mb-2">
            <input type="text" class="form-control" name="options[]" 
                   placeholder="Seçim daxil edin" maxlength="255" pattern="[a-zA-Z0-9\s]+" title="Seçim yalnız hərflər, rəqəmlər və boşluqlardan ibarət ola bilər">
            <button type="button" class="btn btn-outline-danger" 
                    onclick="$(this).closest('.input-group').remove()">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    $('#optionsList').append(optionHtml);
}

// Tip dəyişdikdə seçimləri göstər/gizlət
$(document).ready(function() {
    $('#columnType').on('change', function() {
        if ($(this).val() === 'select') {
            $('#optionsWrapper').slideDown();
        } else {
            $('#optionsWrapper').slideUp();
        }
    });
});
</script>