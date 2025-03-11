@extends('layouts.app')

@section('title', 'Nowy kontrahent')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Nowy kontrahent</h1>
        <a href="{{ route('contractors.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700">
            <i class="fas fa-arrow-left mr-2"></i>
            Powrót
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-6">
        <form action="{{ route('contractors.store') }}" method="POST" class="space-y-6">
            @include('contractors._form')

            <div class="flex justify-end space-x-3">
                <a href="{{ route('contractors.index') }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Anuluj
                </a>
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    Dodaj kontrahenta
                </button>
            </div>
        </form>
    </div>
</div>
@endsection 