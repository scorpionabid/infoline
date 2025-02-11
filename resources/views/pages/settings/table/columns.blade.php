@extends('layouts.app')

@section('title', 'Sütunlar')

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Sütunlar</h6>
            <a href="{{ route('settings.columns.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Yeni Sütun Əlavə Et
            </a>
        </div>
        <div class="card-body">
            @if($columns->isEmpty())
                <div class="alert alert-info text-center">
                    Hələ heç bir sütun yaradılmayıb.
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered" id="columnsTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Ad</th>
                                <th>Tip</th>
                                <th>Son Tarix</th>
                                <th>Status</th>
                                <th>Əməliyyatlar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($columns as $column)
                            <tr>
                                <td>{{ $column->id }}</td>
                                <td>{{ $column->name }}</td>
                                <td>{{ $column->type }}</td>
                                <td>{{ $column->deadline ? \Carbon\Carbon::parse($column->deadline)->format('d.m.Y H:i') : 'Təyin edilməyib' }}</td>
                                <td>
                                    <span class="badge {{ $column->is_active ? 'badge-success' : 'badge-danger' }}">
                                        {{ $column->is_active ? 'Aktiv' : 'Deaktiv' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('settings.columns.edit', $column->id) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('settings.columns.destroy', $column->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Silmək istədiyinizə əminsinizmi?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('#columnsTable').DataTable({
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Azerbaijani.json'
            }
        });
    });
</script>
@endsection