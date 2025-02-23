{{-- resources/views/pages/dashboard/sector-admin/reports/schools-comparison-table.blade.php --}}
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Məktəblər üzrə müqayisəli hesabat</h6>
        <div class="btn-group">
            <button class="btn btn-sm btn-outline-primary" onclick="sortTable('name')">
                Ada görə
            </button>
            <button class="btn btn-sm btn-outline-primary" onclick="sortTable('completion')">
                Doldurulmaya görə
            </button>
            <button class="btn btn-sm btn-outline-primary" onclick="sortTable('date')">
                Tarixə görə
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="schoolsComparisonTable">
                <thead>
                    <tr>
                        <th>Məktəb</th>
                        <th>Doldurulma %</th>
                        <th>Məcburi sütunlar</th>
                        <th>Son yenilənmə</th>
                        <th>Trend</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($schoolsComparison as $school)
                    <tr>
                        <td>{{ $school['name'] }}</td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="progress flex-grow-1 me-2" style="height: 20px;">
                                    <div class="progress-bar {{ 
                                        $school['completion_rate'] < 50 ? 'bg-danger' : 
                                        ($school['completion_rate'] < 80 ? 'bg-warning' : 'bg-success') 
                                    }}" 
                                    role="progressbar" 
                                    style="width: {{ $school['completion_rate'] }}%">
                                        {{ $school['completion_rate'] }}%
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            {{ $school['required_filled'] }}/{{ $school['required_total'] }}
                            @if($school['required_filled'] < $school['required_total'])
                                <span class="badge bg-danger">
                                    {{ $school['required_total'] - $school['required_filled'] }} qalıb
                                </span>
                            @else
                                <span class="badge bg-success">Tamamlanıb</span>
                            @endif
                        </td>
                        <td data-sort="{{ $school['last_update']?->timestamp ?? 0 }}">
                            @if($school['last_update'])
                                {{ $school['last_update']->format('d.m.Y H:i') }}
                                <small class="text-muted d-block">
                                    {{ $school['last_update']->diffForHumans() }}
                                </small>
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @switch($school['trend'])
                                @case('increasing')
                                    <span class="text-success">
                                        <i class="fas fa-arrow-up"></i> Artır
                                    </span>
                                    @break
                                @case('decreasing')
                                    <span class="text-danger">
                                        <i class="fas fa-arrow-down"></i> Azalır
                                    </span>
                                    @break
                                @default
                                    <span class="text-muted">
                                        <i class="fas fa-minus"></i> Sabit
                                    </span>
                            @endswitch
                        </td>
                        <td>
                            @if($school['completion_rate'] >= 80)
                                <span class="badge bg-success">Yaxşı</span>
                            @elseif($school['completion_rate'] >= 50)
                                <span class="badge bg-warning">Normal</span>
                            @else
                                <span class="badge bg-danger">Kritik</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>