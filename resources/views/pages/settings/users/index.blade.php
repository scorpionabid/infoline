@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Page title -->
    <div class="row mb-3">
        <div class="col-12">
            <h1 class="h3">İstifadəçi İdarəetməsi</h1>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-3">
        <div class="card-body">
            <form action="{{ route('settings.users.index') }}" method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">İstifadəçi Tipi</label>
                    <select name="user_type" class="form-select">
                        <option value="">Hamısı</option>
                        <option value="sectoradmin" @selected(request('user_type') == 'sectoradmin')>Sektor Admin</option>
                        <option value="schooladmin" @selected(request('user_type') == 'schooladmin')>Məktəb Admin</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Region</label>
                    <select name="region_id" class="form-select">
                        <option value="">Hamısı</option>
                        @foreach($regions as $region)
                            <option value="{{ $region->id }}" @selected(request('region_id') == $region->id)>
                                {{ $region->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="is_active" class="form-select">
                        <option value="">Hamısı</option>
                        <option value="1" @selected(request('is_active') == '1')>Aktiv</option>
                        <option value="0" @selected(request('is_active') == '0')>Deaktiv</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="fas fa-filter"></i> Filtrlə
                    </button>
                    <a href="{{ route('settings.users.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Təmizlə
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Users list -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">İstifadəçilər</h5>
            <a href="{{ route('settings.users.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Yeni İstifadəçi
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Ad Soyad</th>
                            <th>İstifadəçi Adı</th>
                            <th>Email</th>
                            <th>Tip</th>
                            <th>Region</th>
                            <th>Sektor/Məktəb</th>
                            <th>Status</th>
                            <th>Əməliyyatlar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>{{ $user->first_name }} {{ $user->last_name }}</td>
                                <td>{{ $user->username }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @if($user->user_type == 'sectoradmin')
                                        <span class="badge bg-info">Sektor Admin</span>
                                    @elseif($user->user_type == 'schooladmin')
                                        <span class="badge bg-success">Məktəb Admin</span>
                                    @endif
                                </td>
                                <td>{{ $user->region?->name }}</td>
                                <td>
                                    @if($user->user_type == 'sectoradmin')
                                        {{ $user->sector?->name }}
                                    @else
                                        {{ $user->school?->name }}
                                    @endif
                                </td>
                                <td>
                                    @if($user->is_active)
                                        <span class="badge bg-success">Aktiv</span>
                                    @else
                                        <span class="badge bg-danger">Deaktiv</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('settings.users.edit', $user) }}" 
                                           class="btn btn-sm btn-info" 
                                           title="Düzəliş et">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        @if(!$user->isSuperAdmin())
                                            <button type="button" 
                                                    class="btn btn-sm btn-warning" 
                                                    title="Statusu dəyiş"
                                                    onclick="toggleStatus({{ $user->id }})">
                                                <i class="fas fa-sync"></i>
                                            </button>
                                            
                                            <button type="button" 
                                                    class="btn btn-sm btn-danger" 
                                                    title="Sil"
                                                    onclick="deleteUser({{ $user->id }})">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">İstifadəçi tapılmadı</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $users->withQueryString()->links() }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function toggleStatus(userId) {
    if (confirm('İstifadəçinin statusunu dəyişmək istədiyinizə əminsiniz?')) {
        let form = document.createElement('form');
        form.method = 'POST';
        form.action = `/settings/users/${userId}/status`;
        form.innerHTML = `
            @csrf
            @method('PUT')
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

function deleteUser(userId) {
    if (confirm('İstifadəçini silmək istədiyinizə əminsiniz?')) {
        let form = document.createElement('form');
        form.method = 'POST';
        form.action = `/settings/users/${userId}`;
        form.innerHTML = `
            @csrf
            @method('DELETE')
        `;
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush
@endsection