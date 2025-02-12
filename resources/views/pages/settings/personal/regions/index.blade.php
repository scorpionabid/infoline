@extends("pages.settings.personal.index")

@section('tab-content')
<div class="row">
   <div class="col-12 mb-4">
       <div class="card">
           <div class="card-header d-flex justify-content-between align-items-center">
               <h5 class="mb-0">Regionlar</h5>
               <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#regionModal">
                   <i class="fas fa-plus"></i> Region əlavə et
               </button>
           </div>
           <div class="card-body">
               <div class="table-responsive">
                   <table class="table table-hover">
                       <thead>
                           <tr>
                               <th>Region adı</th>
                               <th>Telefon</th>
                               <th>Sektor sayı</th>
                               <th>Məktəb sayı</th>
                               <th>Əməliyyatlar</th>
                           </tr>
                       </thead>
                       <tbody>
                            @foreach($regions as $region)
                            <tr>
                                <td>{{ $region->name }}</td>
                                <td>{{ $region->phone ?? 'Qeyd edilməyib' }}</td>
                                <td>{{ $region->sectors_count }}</td>
                                <td>{{ $region->schools_count }}</td>
                                <td>
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-outline-primary btn-edit-region" 
                                                data-id="{{ $region->id }}">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        @if($region->sectors_count == 0)
                                        <button class="btn btn-sm btn-outline-danger btn-delete-region" 
                                                data-id="{{ $region->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        @endif
                                    </div>
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

<!-- Region Modal -->
<div class="modal fade" id="regionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Region</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                @csrf
                @method('POST')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Ad</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Telefon</label>
                        <input type="text" name="phone" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Bağla</button>
                    <button type="submit" class="btn btn-primary">Yadda saxla</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Global config
    const regionConfig = {
        urls: {
            edit: "{{ route('settings.personal.regions.edit', ':id') }}",
            update: "{{ route('settings.personal.regions.update', ':id') }}",
            delete: "{{ route('settings.personal.regions.destroy', ':id') }}"
        }
    };
</script>
<script src="{{ asset('js/settings/regions.js') }}"></script>
@endpush
