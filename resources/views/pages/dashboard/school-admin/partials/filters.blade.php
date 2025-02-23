{{-- resources/views/pages/dashboard/school-admin/partials/filters.blade.php --}}
<div class="d-flex gap-3">
    <div class="dropdown">
        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
            Status Filter
        </button>
        <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="#" data-filter="all">Hamısı</a></li>
            <li><a class="dropdown-item" href="#" data-filter="empty">Boş</a></li>
            <li><a class="dropdown-item" href="#" data-filter="filled">Doldurulmuş</a></li>
            <li><a class="dropdown-item" href="#" data-filter="required">Məcburi</a></li>
        </ul>
    </div>

    <div class="search-box">
        <input type="text" class="form-control" placeholder="Axtar..." id="searchInput">
    </div>
</div>