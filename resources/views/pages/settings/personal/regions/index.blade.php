@extends('layouts.app')

@section('title', 'Regionlar')

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

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Regionlar</h6>
            <a href="{{ route('settings.personal.regions.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Yeni Region
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Region</th>
                            <th>Sektor Sayı</th>
                            <th>Məktəb Sayı</th>
                            <th>Status</th>
                            <th>Əməliyyatlar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($regions as $region)
                            <tr @if($region->deleted_at) class="table-secondary" @endif>
                                <td>{{ $region->name }}</td>
                                <td>{{ $region->sectors_count }}</td>
                                <td>{{ $region->schools_count }}</td>
                                <td>
                                    @if($region->deleted_at)
                                        <span class="badge bg-secondary">Silinib</span>
                                    @else
                                        <span class="badge bg-success">Aktiv</span>
                                    @endif
                                </td>
                                <td>
                                    @if(!$region->deleted_at)
                                        <div class="btn-group">
                                            <form action="{{ route('settings.personal.regions.edit', $region->id) }}" 
                                                  method="GET" 
                                                  class="d-inline">
                                                <button type="submit" class="btn btn-sm btn-primary" title="Redaktə et">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                            </form>
                                            
                                            @if($region->sectors_count == 0)
                                                <form action="{{ route('settings.personal.regions.destroy', $region->id) }}" 
                                                      method="POST" 
                                                      class="d-inline"
                                                      onsubmit="return confirm('Bu regionu birdəfəlik silmək istədiyinizdən əminsiniz? Bu əməliyyat geri qaytarila bilməz!')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Sil">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    @else
                                        <div class="btn-group">
                                            <form action="{{ route('settings.personal.regions.restore', $region->id) }}" 
                                                  method="POST" 
                                                  class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success" title="Bərpa et">
                                                    <i class="fas fa-undo"></i>
                                                </button>
                                            </form>
                                            
                                            <form action="{{ route('settings.personal.regions.force-delete', $region->id) }}" 
                                                  method="POST" 
                                                  class="d-inline"
                                                  onsubmit="return confirm('Bu regionu birdəfəlik silmək istədiyinizdən əminsiniz? Bu əməliyyat geri qaytarila bilməz!')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="Tam sil">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">Region tapılmadı</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection