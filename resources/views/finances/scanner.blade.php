@extends('layouts.app')

@section('content')
<div class="py-6">
    <div class="max-w-[1920px] mx-auto sm:px-4 lg:px-6">
        <div class="flex flex-col md:flex-row gap-6">
            <!-- Menu boczne -->
            <div class="md:w-56 flex-shrink-0">
                <div class="bg-white bg-opacity-10 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4">
                        <div class="space-y-2">
                            <a href="{{ route('dashboard') }}" class="flex items-center space-x-2 text-gray-700 hover:text-gray-900 group">
                                <span class="w-6 h-6 rounded-lg bg-gray-100 bg-opacity-10 group-hover:bg-opacity-20 flex items-center justify-center">
                                    <i class="fas fa-home text-gray-600 text-xs"></i>
                                </span>
                                <span class="text-sm">Panel Główny</span>
                            </a>
                            <a href="{{ route('finances.index') }}" class="flex items-center space-x-2 text-gray-700 hover:text-gray-900 group">
                                <span class="w-6 h-6 rounded-lg bg-gray-100 bg-opacity-10 group-hover:bg-opacity-20 flex items-center justify-center">
                                    <i class="fas fa-file-invoice-dollar text-gray-600 text-xs"></i>
                                </span>
                                <span class="text-sm">Finanse</span>
                            </a>
                            <a href="{{ route('finances.scanner') }}" class="flex items-center space-x-2 text-blue-600 group">
                                <span class="w-6 h-6 rounded-lg bg-blue-100 bg-opacity-10 group-hover:bg-opacity-20 flex items-center justify-center">
                                    <i class="fas fa-scanner text-blue-600 text-xs"></i>
                                </span>
                                <span class="text-sm">Skaner dokumentów</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Główna zawartość -->
            <div class="flex-1">
                <div class="mb-6">
                    <h1 class="text-2xl font-semibold text-gray-800">Skaner dokumentów</h1>
                    <p class="text-sm text-gray-600">Skanuj i zarządzaj dokumentami</p>
                </div>

                <!-- Obszar skanowania -->
                <div class="bg-white bg-opacity-10 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-center">
                        <div class="mb-6">
                            <div class="w-24 h-24 mx-auto bg-blue-100 bg-opacity-10 rounded-xl flex items-center justify-center">
                                <i class="fas fa-file-upload text-blue-600 text-4xl"></i>
                            </div>
                            <h3 class="mt-4 text-lg font-semibold text-gray-800">Przeciągnij i upuść pliki</h3>
                            <p class="text-sm text-gray-600">lub</p>
                        </div>
                        
                        <label class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-800 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150 cursor-pointer">
                            <span>Wybierz plik</span>
                            <input type="file" class="hidden" accept="image/*,.pdf" multiple>
                        </label>

                        <p class="mt-2 text-xs text-gray-600">Obsługiwane formaty: JPG, PNG, PDF</p>
                    </div>
                </div>

                <!-- Lista ostatnio zeskanowanych dokumentów -->
                <div class="mt-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Ostatnio zeskanowane</h3>
                    <div class="bg-white bg-opacity-10 backdrop-blur-sm overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-4">
                            <div class="space-y-3">
                                <!-- Przykładowy dokument -->
                                <div class="flex items-center justify-between p-3 bg-white bg-opacity-10 rounded-lg">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-blue-100 bg-opacity-10 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-file-pdf text-blue-600"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-semibold text-gray-800">Faktura VAT 123/2024</h4>
                                            <p class="text-xs text-gray-600">Zeskanowano: 22.02.2024</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <button class="p-2 text-gray-600 hover:text-gray-900">
                                            <i class="fas fa-download"></i>
                                        </button>
                                        <button class="p-2 text-gray-600 hover:text-gray-900">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 