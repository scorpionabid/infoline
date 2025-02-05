@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h1>Dashboard</h1>
            <p>Xoş gəlmisiniz, {{ auth()->user()->name }}</p>
            <p>Role: {{ auth()->user()->role }}</p>
        </div>
    </div>
</div>
@endsection