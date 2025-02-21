<!-- Sütun Modal -->
<div class="modal fade" id="columnModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="columnModalTitle">Yeni Sütun</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="columnForm">
                <div class="modal-body">
                    <input type="hidden" name="category_id" id="columnCategoryId">
                    <input type="hidden" name="column_id" id="columnId">

                    <!-- Əsas Məlumatlar -->
                    <div class="mb-3">
                        <label class="form-label">Sütun Adı</label>
                        <input type="text" class="form-control" name="name" id="columnName" required>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Təsvir</label>
                        <textarea class="form-control" name="description" id="columnDescription" rows="2"></textarea>
                        <div class="invalid-feedback"></div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Məlumat Növü</label>
                                <select class="form-select" name="type" id="columnType" required>
                                    <option value="text">Mətn</option>
                                    <option value="number">Rəqəm</option>
                                    <option value="date">Tarix</option>
                                    <option value="select">Seçim</option>
                                    <option value="textarea">Uzun Mətn</option>
                                </select>
                                <div class="invalid-feedback"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Tələb olunur?</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" name="required" id="columnRequired">
                                    <label class="form-check-label">Bəli</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Seçim variantları (yalnız select type üçün) -->
                    <div class="mb-3 d-none" id="optionsGroup">
                        <label class="form-label">Seçim Variantları</label>
                        <div id="optionsContainer">
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" name="options[]" placeholder="Variant">
                                <button type="button" class="btn btn-outline-danger" onclick="removeOption(this)">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm" onclick="addOption()">
                            <i class="fas fa-plus"></i> Yeni variant
                        </button>
                    </div>

                    <!-- Validasiya qaydaları -->
                    <div class="mb-3">
                        <label class="form-label">Validasiya Qaydaları</label>
                        <div id="validationRulesContainer">
                            <!-- Mətn validasiyası -->
                            <div class="validation-rules text d-none">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="validation_rules[min_length]" id="minLength">
                                    <label class="form-check-label">Minimum uzunluq</label>
                                    <input type="number" class="form-control form-control-sm w-25 d-inline-block ms-2" disabled>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="validation_rules[max_length]" id="maxLength">
                                    <label class="form-check-label">Maksimum uzunluq</label>
                                    <input type="number" class="form-control form-control-sm w-25 d-inline-block ms-2" disabled>
                                </div>
                            </div>

                            <!-- Rəqəm validasiyası -->
                            <div class="validation-rules number d-none">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="validation_rules[min]" id="minValue">
                                    <label class="form-check-label">Minimum dəyər</label>
                                    <input type="number" class="form-control form-control-sm w-25 d-inline-block ms-2" disabled>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="validation_rules[max]" id="maxValue">
                                    <label class="form-check-label">Maksimum dəyər</label>
                                    <input type="number" class="form-control form-control-sm w-25 d-inline-block ms-2" disabled>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Status (yalnız redaktə zamanı) -->
                    <div class="mb-3 d-none" id="columnStatusGroup">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="status" id="columnStatus">
                            <label class="form-check-label">Aktiv</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Ləğv et</button>
                    <button type="submit" class="btn btn-primary">Yadda saxla</button>
                </div>
            </form>
        </div>
    </div>
</div>