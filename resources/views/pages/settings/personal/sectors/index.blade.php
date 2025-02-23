@extends('layouts.app')

@section('title', 'Sektorlar')

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="mb-3 d-flex justify-content-end">
        <a href="{{ route('settings.personal.sectors.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-1"></i> Yeni Sektor
        </a>
    </div>

    <div class="row">
        @foreach($regions as $region)
        <div class="col-md-6 col-xl-4 mb-4">
            <div class="card h-100">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">{{ $region->name }}</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Sektor</th>
                                    <th>Admin</th>
                                    <th>Məktəb</th>
                                    <th>Əməliyyatlar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($region->sectors as $sector)
                                    <tr>
                                        <td>{{ $sector->name }}</td>
                                        <td>
                                            @if($sector->admin)
                                                {{ $sector->admin->full_name }}
                                            @else
                                                <span class="text-muted">Təyin edilməyib</span>
                                            @endif
                                        </td>
                                        <td>{{ $sector->schools_count }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <form action="{{ route('settings.personal.sectors.edit', $sector->id) }}" 
                                                      method="GET" 
                                                      class="d-inline">
                                                    <button type="submit" class="btn btn-sm btn-primary" title="Redaktə et">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                </form>
                                                
                                                @if($sector->schools_count == 0)
                                                    <form action="{{ route('settings.personal.sectors.destroy', $sector->id) }}" 
                                                          method="POST" 
                                                          class="d-inline"
                                                          onsubmit="return confirm('Bu sektoru birdəfəlik silmək istədiyinizdən əminsiniz? Bu əməliyyat geri qaytarila bilməz!')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Sil">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">Bu regionda sektor yoxdur</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>


@endsection