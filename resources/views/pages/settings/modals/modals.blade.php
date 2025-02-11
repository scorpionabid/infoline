{{-- resources/views/settings/modals/modals.blade.php --}}

{{-- Region Modal --}}
<div class="modal fade" id="regionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('settings.personal.regions.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Region əlavə et</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Region adı</label>
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

{{-- School Admin Modal --}}
<div class="modal fade" id="schoolAdminModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('settings.personal.users.store') }}" method="POST">
                @csrf   
                <input type="hidden" name="user_type" value="schooladmin">
                <div class="modal-header">
                    <h5 class="modal-title">Məktəb admini əlavə et</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Ad və Soyad</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">UTIS kod</label>
                        <input type="text" name="utis_code" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Məktəb</label>
                        <select name="school_id" class="form-select" required>
                            @foreach($schools as $school)
                                <option value="{{ $school->id }}">{{ $school->name }}</option>
                            @endforeach
                        </select>
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