{{-- resources/views/pages/dashboard/sector-admin/components/stats-panel.blade.php --}}
<div class="row mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card bg-primary text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="h3 mb-0">{{ $statistics['total_schools'] }}</div>
                        <div>Ümumi məktəb sayı</div>
                    </div>
                    <div class="fa-2x">
                        <i class="fas fa-school"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card bg-success text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="h3 mb-0">{{ $statistics['completion_rate'] }}%</div>
                        <div>Orta doldurulma faizi</div>
                    </div>
                    <div class="fa-2x">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card bg-warning text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="h3 mb-0">{{ $statistics['active_schools_last_week'] }}</div>
                        <div>Son həftə aktiv məktəb</div>
                    </div>
                    <div class="fa-2x">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card bg-info text-white h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="h3 mb-0">{{ count($upcomingDeadlines) }}</div>
                        <div>Yaxınlaşan son tarix</div>
                    </div>
                    <div class="fa-2x">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>