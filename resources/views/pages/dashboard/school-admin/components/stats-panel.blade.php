{{-- resources/views/pages/dashboard/school-admin/components/stats-panel.blade.php --}}
<div class="row">
    <div class="col-xl-3 col-md-6">
        <div class="card bg-primary text-white mb-4">
            <div class="card-body">
                <h2 class="mb-0">{{ $statistics['completion_rate'] }}%</h2>
                <div>Tamamlanma faizi</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card bg-warning text-white mb-4">
            <div class="card-body">
                <h2 class="mb-0">{{ $statistics['empty_columns'] }}</h2>
                <div>Boş sütunlar</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card bg-success text-white mb-4">
            <div class="card-body">
                <h2 class="mb-0">{{ $statistics['filled_required_columns'] }}/{{ $statistics['required_columns'] }}</h2>
                <div>Məcburi sütunlar</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card bg-info text-white mb-4">
            <div class="card-body">
                <h2 class="mb-0">{{ $upcomingDeadlines->count() }}</h2>
                <div>Yaxınlaşan son tarix</div>
            </div>
        </div>
    </div>
</div>