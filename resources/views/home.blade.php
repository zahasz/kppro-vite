@extends('layouts.app')

@section('title', 'Strona główna')

@section('header', 'Witaj w KPpro')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Witaj w systemie KPpro</h5>
                <p class="card-text">Wybierz jedną z opcji w menu bocznym lub przejdź do panelu głównego.</p>
                <a href="{{ route('dashboard') }}" class="btn btn-primary">Przejdź do panelu</a>
            </div>
        </div>
    </div>
</div>
@endsection 