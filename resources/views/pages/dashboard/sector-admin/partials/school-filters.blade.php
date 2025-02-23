{{-- resources/views/pages/dashboard/sector-admin/partials/school-filters.blade.php --}}
<div class="d-flex gap-2 align-items-center">
    <div class="input-group">
        <input type="text" 
               class="form-control" 
               placeholder="Məktəb axtar..." 
               id="schoolSearch">
        <span class="input-group-text">
            <i class="fas fa-search"></i>
        </span>
    </div>

    <select class="form-select" id="statusFilter">
        <option value="">Bütün statuslar</option>
        <option value="completed">Tamamlanıb</option>
        <option value="in_progress">Davam edir</option>
        <option value="warning">Diqqət tələb edən</option>
        <option value="critical">Kritik</option>
    </select>

    <select class="form-select" id="completionFilter">
        <option value="">Doldurulma %</option>
        <option value="0-50">0-50%</option>
        <option value="51-80">51-80%</option>
        <option value="81-100">81-100%</option>
    </select>
</div>