@extends('layouts.app')

@section('title', 'Sektorlar')

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
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
                                    <th>Sektor İstifadəçisi</th>
                                    <th>Məktəb</th>
                                    <th>Əməliyyatlar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($region->sectors as $sector)
                                    <tr>
                                        <td>{{ $sector->name }}</td>
                                        <td id="admin-cell-{{ $sector->id }}">
                                            @if($sector->admin)
                                                <div class="d-flex align-items-center justify-content-between admin-info">
                                                    <div>
                                                        <span class="admin-name">{{ $sector->admin->first_name }} {{ $sector->admin->last_name }}</span>
                                                        <small class="text-muted d-block">{{ $sector->admin->email }}</small>
                                                    </div>
                                                    <div class="admin-actions">
                                                        <form action="{{ route('settings.personal.sectors.admin.remove', $sector) }}" 
                                                              method="POST" 
                                                              class="d-inline ms-2"
                                                              onsubmit="return confirm('Admini silmək istədiyinizə əminsiniz?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger" title="Admini sil">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="d-flex gap-2 justify-content-start">
                                                    <!-- YENİ: Modal əvəzinə birbaşa link -->
                                                    <a href="{{ route('settings.personal.sectors.admin.create', $sector) }}" 
                                                       class="btn btn-sm btn-success">
                                                        <i class="fas fa-user-plus"></i> Yeni Admin Təyin Et
                                                    </a>
                                                </div>
                                            @endif
                                        </td>
                                        <td id="user-cell-{{ $sector->id }}">
                                            @if($sector->admin)
                                                <div class="user-info">
                                                    <span class="user-name">{{ $sector->admin->first_name }} {{ $sector->admin->last_name }}</span>
                                                    <small class="text-muted d-block">{{ $sector->admin->email }}</small>
                                                </div>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>{{ $sector->schools_count }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('settings.personal.sectors.edit', $sector) }}" 
                                                   class="btn btn-sm btn-primary" 
                                                   title="Redaktə et">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                
                                                @if($sector->schools_count == 0)
                                                    <form action="{{ route('settings.personal.sectors.destroy', $sector) }}" 
                                                          method="POST" 
                                                          class="d-inline"
                                                          onsubmit="return confirm('Bu sektoru birdəfəlik silmək istədiyinizdən əminsiniz?')">
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
                                        <td colspan="5" class="text-center">Bu regionda sektor yoxdur</td>
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