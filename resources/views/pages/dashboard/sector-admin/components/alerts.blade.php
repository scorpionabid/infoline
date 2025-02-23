{{-- resources/views/pages/dashboard/sector-admin/components/alerts.blade.php --}}
@if($criticalSchools->count() > 0)
<div class="alert alert-danger">
    <h5 class="alert-heading">
        <i class="fas fa-exclamation-triangle"></i> 
        Diqqət tələb edən məktəblər!
    </h5>
    <ul class="mb-0">
        @foreach($criticalSchools as $school)
        <li>
            {{ $school['name'] }} - 
            @if($school['missing_required'] > 0)
                {{ $school['missing_required'] }} məcburi sütun doldurulmayıb,
            @endif
            Doldurulma: {{ $school['completion_rate'] }}%
        </li>
        @endforeach
    </ul>
</div>
@endif

@if(count($upcomingDeadlines) > 0)
<div class="alert alert-warning">
    <h5 class="alert-heading">
        <i class="fas fa-clock"></i>
        Yaxınlaşan son tarixlər
    </h5>
    <ul class="mb-0">
        @foreach($upcomingDeadlines as $deadline)
        <li>
            {{ $deadline['column']->name }} - {{ $deadline['deadline']->format('d.m.Y') }}
            ({{ $deadline['incomplete_schools']->count() }} məktəb)
        </li>
        @endforeach
    </ul>
</div>
@endif