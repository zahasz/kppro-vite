@props(['title' => null, 'header' => null])

@extends('layouts.app')

@section('title', $title ?? config('app.name', 'Laravel'))

@section('header', $header ?? '')

@section('content')
    {{ $slot }}
@endsection 