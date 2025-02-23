{{-- resources/views/pages/dashboard/school-admin/components/notifications.blade.php --}}
@if($emptyRequiredColumns->count() > 0 || $upcomingDeadlines->count() > 0 || $newColumns->count() > 0)
<div class="row">
    <div class="col-12">
        @if($emptyRequiredColumns->count() > 0)
        <div class="alert alert-danger" role="alert">
            <h5 class="alert-heading">Məcburi sütunlar!</h5>
            <p class="mb-0">{{ $emptyRequiredColumns->count() }} məcburi sütun doldurulmayıb.</p>
        </div>
        @endif

        @if($upcomingDeadlines->count() > 0)
        <div class="alert alert-warning" role="alert">
            <h5 class="alert-heading">Son tarix yaxınlaşır!</h5>
            <ul class="mb-0">
                @foreach($upcomingDeadlines as $column)
                <li>{{ $column->name }} - {{ $column->end_date->format('d.m.Y') }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        @if($newColumns->count() > 0)
        <div class="alert alert-info" role="alert">
            <h5 class="alert-heading">Yeni sütunlar əlavə edilib!</h5>
            <ul class="mb-0">
                @foreach($newColumns as $column)
                <li>{{ $column->name }} ({{ $column->category->name }})</li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>
</div>
@endif