@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-2xl font-semibold mb-6">Faktury</h1>
    
    <div class="bg-white shadow overflow-hidden rounded-lg mb-6">
        <div class="flex flex-wrap">
            <a href="{{ route('finance.invoices') }}" class="px-6 py-3 border-b-2 {{ request()->routeIs('finance.invoices') ? 'border-blue-500 text-blue-600' : 'border-transparent hover:border-gray-200' }}">
                Wszystkie
            </a>
            <a href="{{ route('finance.invoices.sales') }}" class="px-6 py-3 border-b-2 {{ request()->routeIs('finance.invoices.sales') ? 'border-blue-500 text-blue-600' : 'border-transparent hover:border-gray-200' }}">
                Sprzedaż
            </a>
            <a href="{{ route('finance.invoices.purchases') }}" class="px-6 py-3 border-b-2 {{ request()->routeIs('finance.invoices.purchases') ? 'border-blue-500 text-blue-600' : 'border-transparent hover:border-gray-200' }}">
                Zakupy
            </a>
            <a href="{{ route('finance.invoices.subscriptions') }}" class="px-6 py-3 border-b-2 {{ request()->routeIs('finance.invoices.subscriptions') ? 'border-blue-500 text-blue-600' : 'border-transparent hover:border-gray-200' }}">
                Subskrypcje
            </a>
        </div>
    </div>
    
    <div class="bg-white shadow overflow-hidden rounded-lg">
        <div class="p-6">
            <p>Ten widok jest obecnie w trakcie implementacji.</p>
        </div>
    </div>
</div>
@endsection 