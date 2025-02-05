@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <h2 class="mb-4">Məktəb Tənzimləmələri</h2>

            <div class="card">
                <div class="card-body">
                    <form id="schoolSettingsForm">
                        <div class="mb-3">
                            <label class="form-label">Məktəb adı</label>
                            <input type="text" class="form-control" name="name" value="{{ auth()->user()->school->name ?? '' }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Telefon</label>
                            <input type="tel" class="form-control" name="phone" value="{{ auth()->user()->school->phone ?? '' }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" value="{{ auth()->user()->school->email ?? '' }}">
                        </div>
                        <button type="submit" class="btn btn-primary">Yadda saxla</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection