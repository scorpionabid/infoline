@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <h2 class="mb-4">Sistem Tənzimləmələri</h2>

            <div class="card">
                <div class="card-body">
                    <!-- Tənzimləmələr menyusu -->
                    <div class="settings-menu">
                        @if(auth()->user()->isSuperAdmin())
                            <a href="{{ route('settings.categories') }}" class="btn btn-primary mb-2">Kateqoriyalar</a>
                            <a href="{{ route('settings.schools') }}" class="btn btn-primary mb-2">Məktəblər</a>
                        @endif

                        @if(auth()->user()->isSectorAdmin())
                            <a href="{{ route('settings.sector') }}" class="btn btn-primary mb-2">Sektor Tənzimləmələri</a>
                        @endif

                        @if(auth()->user()->isSchoolAdmin())
                            <a href="{{ route('settings.school') }}" class="btn btn-primary mb-2">Məktəb Tənzimləmələri</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection