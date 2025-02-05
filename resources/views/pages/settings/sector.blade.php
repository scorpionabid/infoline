@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <h2 class="mb-4">Sektor Tənzimləmələri</h2>

            <div class="card">
                <div class="card-body">
                    <form id="sectorSettingsForm">
                        <div class="mb-3">
                            <label class="form-label">Sektor adı</label>
                            <input type="text" class="form-control" name="name" value="{{ auth()->user()->sector->name ?? '' }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Region</label>
                            <select class="form-select" name="region_id" required>
                                <!-- JavaScript ilə doldurulacaq -->
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Yadda saxla</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection