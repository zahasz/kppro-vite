@extends('layouts.admin')

@section('header')
    Dashboard
@endsection

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="text-2xl font-semibold text-gray-900">{{ $stats['users_count'] }}</div>
            <div class="text-sm text-gray-600">Użytkownicy</div>
        </div>
    </div>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="text-2xl font-semibold text-gray-900">{{ $stats['roles_count'] }}</div>
            <div class="text-sm text-gray-600">Role</div>
        </div>
    </div>

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6">
            <div class="text-2xl font-semibold text-gray-900">{{ $stats['permissions_count'] }}</div>
            <div class="text-sm text-gray-600">Uprawnienia</div>
        </div>
    </div>
</div>
@endsection 