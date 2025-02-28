<!-- Sütun Əlavə Et və Redaktə Et Modal -->
<div class="modal fade" id="columnModal" tabindex="-1" aria-labelledby="columnModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="columnForm" action="<?php echo e(route('settings.table.columns.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="_method" id="columnMethod" value="POST">
                <input type="hidden" name="id" id="columnId">
                <input type="hidden" name="category_id" id="columnCategoryId" value="<?php echo e($selectedCategory->id ?? ''); ?>">
                
                <div class="modal-header">
                    <h5 class="modal-title" id="columnModalTitle">Yeni Sütun</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Form xətaları -->
                    <div class="form-errors alert alert-danger" style="display: none;"></div>
                    
                    <div class="mb-3">
                        <label for="columnName" class="form-label">Sütun adı <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="columnName" name="name" required>
                        <div class="invalid-feedback">
                            Sütun adı minimum 2 simvol olmalıdır
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="columnDescription" class="form-label">Təsvir</label>
                        <textarea class="form-control" id="columnDescription" name="description" rows="2"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="columnDataType" class="form-label">Məlumat növü <span class="text-danger">*</span></label>
                            <select class="form-select" id="columnDataType" name="data_type" required>
                                <option value="text">Mətn</option>
                                <option value="number">Rəqəm</option>
                                <option value="date">Tarix</option>
                                <option value="select">Seçim</option>
                                <option value="file">Fayl</option>
                                <option value="textarea">Mətn sahəsi</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <div class="form-check mt-4">
                                <input class="form-check-input" type="checkbox" id="columnIsRequired" name="is_required">
                                <label class="form-check-label" for="columnIsRequired">
                                    Məcburi sahə
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Məlumat növünə görə əlavə sahələr -->
                    <div id="textSection" class="type-specific-section">
                        <div class="mb-3">
                            <label for="columnInputLimit" class="form-label">Maksimum simvol limiti</label>
                            <input type="number" class="form-control" id="columnInputLimit" name="input_limit" min="1" max="1000">
                            <div class="form-text">Boş buraxsanız, limit olmayacaq</div>
                        </div>
                    </div>
                    
                    <div id="numberSection" class="type-specific-section" style="display: none;">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="columnMinValue" class="form-label">Minimum dəyər</label>
                                <input type="number" class="form-control" id="columnMinValue" name="min_value">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="columnMaxValue" class="form-label">Maksimum dəyər</label>
                                <input type="number" class="form-control" id="columnMaxValue" name="max_value">
                            </div>
                        </div>
                    </div>
                    
                    <div id="dateSection" class="type-specific-section" style="display: none;">
                        <div class="mb-3">
                            <label for="columnDateFormat" class="form-label">Tarix formatı</label>
                            <select class="form-select" id="columnDateFormat" name="date_format">
                                <option value="Y-m-d">YYYY-MM-DD (2023-12-31)</option>
                                <option value="d.m.Y">DD.MM.YYYY (31.12.2023)</option>
                                <option value="m/d/Y">MM/DD/YYYY (12/31/2023)</option>
                                <option value="Y-m-d H:i">YYYY-MM-DD HH:MM (2023-12-31 14:30)</option>
                            </select>
                        </div>
                    </div>
                    
                    <div id="selectSection" class="type-specific-section" style="display: none;">
                        <div id="optionsSection">
                            <h5>Seçimlər</h5>
                            <p class="text-muted small">Hər bir seçim üçün dəyər və etiket daxil edin.</p>
                            <div id="optionsList">
                                <!-- Seçimlər JavaScript ilə əlavə ediləcək -->
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="ColumnOperations.addOption()">
                                <i class="fas fa-plus"></i> Seçim əlavə et
                            </button>
                        </div>
                    </div>
                    
                    <div id="fileSection" class="type-specific-section" style="display: none;">
                        <div id="fileTypesSection" class="mb-3">
                            <label for="columnFileTypes" class="form-label">İcazə verilən fayl növləri</label>
                            <select class="form-select" id="columnFileTypes" name="file_types[]" multiple>
                                <option value="pdf">PDF</option>
                                <option value="doc">DOC/DOCX</option>
                                <option value="xls">XLS/XLSX</option>
                                <option value="jpg">JPG/JPEG</option>
                                <option value="png">PNG</option>
                                <option value="zip">ZIP/RAR</option>
                            </select>
                            <div class="form-text">Bir neçə seçim etmək üçün CTRL düyməsini basılı saxlayın</div>
                        </div>
                    </div>
                    
                    <div id="textareaSection" class="type-specific-section" style="display: none;">
                        <div class="mb-3">
                            <label for="columnRows" class="form-label">Sətir sayı</label>
                            <input type="number" class="form-control" id="columnRows" name="rows" min="2" max="10" value="3">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bağla</button>
                    <button type="submit" class="btn btn-primary">Yadda saxla</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Sütunu Sil Modal -->
<div class="modal fade" id="deleteColumnModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sütunu sil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Bu sütunu silmək istədiyinizə əminsiniz?</p>
                <p class="text-danger">Diqqət: Bu əməliyyat geri qaytarıla bilməz və bütün əlaqəli məlumatlar silinəcək.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Ləğv et</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteColumn">Sil</button>
            </div>
        </div>
    </div>
</div>
<?php /**PATH /Users/home/Library/CloudStorage/OneDrive-BureauonICTforEducation,MinistryofEducation/infoline_app/resources/views/partials/settings/table/modals/column.blade.php ENDPATH**/ ?>