{{-- resources/views/pages/dashboard/sector-admin/components/schools-table.blade.php --}}
<div class="table-responsive">
    <table class="table table-hover align-middle">
        <thead>
            <tr>
                <th>Məktəb</th>
                <th>Admin</th>
                <th>Doldurulma</th>
                <th>Son yeniləmə</th>
                <th>Status</th>
                <th>Əməliyyatlar</th>
            </tr>
        </thead>
        <tbody>
            @foreach($schools as $school)
            <tr>
                <td>{{ $school['name'] }}</td>
                <td>
                    @if($school['admin'])
                        {{ $school['admin']['name'] }}
                    @else
                        <span class="badge bg-danger">Admin təyin edilməyib</span>
                    @endif
                </td>
                <td>
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar {{ $school['completion_rate'] < 50 ? 'bg-danger' : ($school['completion_rate'] < 100 ? 'bg-warning' : 'bg-success') }}" 
                             role="progressbar" 
                             style="width: {{ $school['completion_rate'] }}%">
                            {{ $school['completion_rate'] }}%
                        </div>
                    </div>
                </td>
                <td>
                    @if($school['last_update'])
                        {{ Carbon\Carbon::parse($school['last_update'])->format('d.m.Y H:i') }}
                    @else
                        -
                    @endif
                </td>
                <td>
                    @switch($school['status'])
                        @case('completed')
                            <span class="badge bg-success">Tamamlanıb</span>
                            @break
                        @case('in_progress')
                            <span class="badge bg-primary">Davam edir</span>
                            @break
                        @case('warning')
                            <span class="badge bg-warning">Diqqət!</span>
                            @break
                        @case('critical')
                            <span class="badge bg-danger">Kritik!</span>
                            @break
                    @endswitch
                </td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button type="button" 
                                class="btn btn-primary" 
                                onclick="viewSchoolDetails({{ $school['id'] }})">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button type="button" 
                                class="btn btn-warning" 
                                onclick="assignAdmin({{ $school['id'] }})">
                            <i class="fas fa-user-plus"></i>
                        </button>
                        <button type="button" 
                                class="btn btn-info" 
                                onclick="exportSchoolData({{ $school['id'] }})">
                            <i class="fas fa-download"></i>
                        </button>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>