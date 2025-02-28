<!-- Kateqoriya Əlavə Et və Redaktə Et Modal -->
<div class="modal fade" id="categoryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="categoryModalTitle">Yeni Kateqoriya</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="categoryForm" action="{{ route('settings.table.categories.store') }}" method="POST">
                @csrf
                <input type="hidden" name="_method" id="categoryMethod" value="POST">
                <input type="hidden" name="id" id="categoryId">
                
                <div class="modal-body">
                    <!-- Form xətaları -->
                    <div class="form-errors alert alert-danger" style="display: none;"></div>

                    <!-- Kateqoriya adı -->
                    <div class="mb-3">
                        <label for="categoryName" class="form-label">Kateqoriya adı <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="categoryName" name="name" required>
                        <div class="invalid-feedback">
                            Kateqoriya adı minimum 2 simvol olmalıdır
                        </div>
                    </div>

                    <!-- Kateqoriya təsviri -->
                    <div class="mb-3">
                        <label for="categoryDescription" class="form-label">Təsvir</label>
                        <textarea class="form-control" id="categoryDescription" name="description" rows="3"></textarea>
                        <div class="form-text">
                            Maximum 1000 simvol
                        </div>
                    </div>
                    
                    <!-- Təyinat tipi -->
                    <div class="mb-3">
                        <label class="form-label">Tətbiq ediləcək</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="assigned_type" id="assignmentAll" value="all" checked>
                            <label class="form-check-label" for="assignmentAll">Hamısı</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="assigned_type" id="assignmentSector" value="sector">
                            <label class="form-check-label" for="assignmentSector">Sektorlar</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="assigned_type" id="assignmentSchool" value="school">
                            <label class="form-check-label" for="assignmentSchool">Məktəblər</label>
                        </div>
                    </div>
                    
                    <!-- Sektor seçimi -->
                    <div id="sectorSelection" class="mb-3" style="display: none;">
                        <label for="assignedSectors" class="form-label">Sektorlar <span class="text-danger">*</span></label>
                        <select class="form-select select2" id="assignedSectors" name="assigned_sectors[]" multiple>
                            @foreach($sectors as $sector)
                                <option value="{{ $sector->id }}">{{ $sector->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Məktəb seçimi -->
                    <div id="schoolSelection" class="mb-3" style="display: none;">
                        <label for="assignedSchools" class="form-label">Məktəblər <span class="text-danger">*</span></label>
                        <select class="form-select select2" id="assignedSchools" name="assigned_schools[]" multiple>
                            @foreach($schools as $school)
                                <option value="{{ $school->id }}">{{ $school->name }}</option>
                            @endforeach
                        </select>
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
