@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Son Tarixlər</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Kateqoriya</th>
                                    <th>Son Tarix</th>
                                    <th>Xəbərdarlıq Günləri</th>
                                    <th>Əməliyyatlar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($deadlines as $deadline)
                                <tr>
                                    <td>{{ $deadline->category->name }}</td>
                                    <td>{{ $deadline->deadline->format('d.m.Y H:i') }}</td>
                                    <td>{{ $deadline->warning_days }}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-primary" data-toggle="modal" data-target="#editDeadline{{ $deadline->id }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <form action="{{ route('settings.table.deadlines.destroy', $deadline) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Əminsiniz?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection