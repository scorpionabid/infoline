{{-- resources/views/pages/dashboard/sector-admin/reports/category-analysis.blade.php --}}
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Kateqoriyalar üzrə analiz</h6>
        <div class="btn-group">
            <button class="btn btn-sm btn-outline-secondary" type="button" data-view="chart" onclick="toggleView('chart')">
                <i class="fas fa-chart-bar"></i> Qrafik
            </button>
            <button class="btn btn-sm btn-outline-secondary" type="button" data-view="table" onclick="toggleView('table')">
                <i class="fas fa-table"></i> Cədvəl
            </button>
        </div>
    </div>
    <div class="card-body">
        {{-- Qrafik görünüşü --}}
        <div id="categoryChartView">
            <canvas id="categoryAnalysisChart" height="300"></canvas>
        </div>

        {{-- Cədvəl görünüşü --}}
        <div id="categoryTableView" class="d-none">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Kateqoriya</th>
                            <th>Ümumi sütun</th>
                            <th>Məcburi sütun</th>
                            <th>Doldurulma %</th>
                            <th>Son həftə aktivlik</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categoryAnalysis as $category)
                        <tr>
                            <td>{{ $category['name'] }}</td>
                            <td>{{ $category['columns_count'] }}</td>
                            <td>
                                {{ $category['required_columns'] }}
                                @if($category['required_columns'] > 0)
                                    <span class="badge bg-primary">Məcburi</span>
                                @endif
                            </td>
                            <td>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar bg-info" 
                                         role="progressbar" 
                                         style="width: {{ $category['completion_rate'] }}%">
                                        {{ number_format($category['completion_rate'], 1) }}%
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                @if($category['weekly_activity'] > 0)
                                    <span class="badge bg-success">{{ $category['weekly_activity'] }} dəyişiklik</span>
                                @else
                                    <span class="badge bg-secondary">Aktivlik yoxdur</span>
                                @endif
                            </td>
                            <td>
                                @if($category['completion_rate'] >= 90)
                                    <span class="badge bg-success">Əla</span>
                                @elseif($category['completion_rate'] >= 70)
                                    <span class="badge bg-info">Yaxşı</span>
                                @elseif($category['completion_rate'] >= 50)
                                    <span class="badge bg-warning">Normal</span>
                                @else
                                    <span class="badge bg-danger">Zəif</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Kateqoriya analiz qrafikini hazırla
    const ctx = document.getElementById('categoryAnalysisChart').getContext('2d');
    const categoryData = @json($categoryAnalysis);

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: categoryData.map(c => c.name),
            datasets: [{
                label: 'Doldurulma faizi',
                data: categoryData.map(c => c.completion_rate),
                backgroundColor: categoryData.map(c => {
                    if (c.completion_rate >= 90) return 'rgba(40, 167, 69, 0.8)';
                    if (c.completion_rate >= 70) return 'rgba(23, 162, 184, 0.8)';
                    if (c.completion_rate >= 50) return 'rgba(255, 193, 7, 0.8)';
                    return 'rgba(220, 53, 69, 0.8)';
                }),
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: value => value + '%'
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: context => {
                            const category = categoryData[context.dataIndex];
                            return [
                                `Doldurulma: ${category.completion_rate}%`,
                                `Ümumi sütun: ${category.columns_count}`,
                                `Məcburi: ${category.required_columns}`
                            ];
                        }
                    }
                }
            }
        }
    });
});

// Görünüş dəyişdirmə funksiyası
function toggleView(view) {
    const chartView = document.getElementById('categoryChartView');
    const tableView = document.getElementById('categoryTableView');
    const buttons = document.querySelectorAll('.btn-group button');

    if (view === 'chart') {
        chartView.classList.remove('d-none');
        tableView.classList.add('d-none');
    } else {
        chartView.classList.add('d-none');
        tableView.classList.remove('d-none');
    }

    // Aktiv button stilini dəyiş
    buttons.forEach(btn => {
        if (btn.dataset.view === view) {
            btn.classList.remove('btn-outline-secondary');
            btn.classList.add('btn-secondary');
        } else {
            btn.classList.add('btn-outline-secondary');
            btn.classList.remove('btn-secondary');
        }
    });
}
</script>
@endpush