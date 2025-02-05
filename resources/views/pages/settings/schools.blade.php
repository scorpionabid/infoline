@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Məktəblər</h2>
                <div>
                    <button type="button" class="btn btn-success me-2" id="importExcel">
                        Excel Import
                    </button>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSchoolModal">
                        Yeni Məktəb
                    </button>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="schoolsTable">
                            <thead>
                                <tr>
                                    <th>Ad</th>
                                    <th>Sektor</th>
                                    <th>Region</th>
                                    <th>Admin sayı</th>
                                    <th>Əməliyyatlar</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- JavaScript ilə doldurulacaq -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection