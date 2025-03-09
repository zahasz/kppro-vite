@props(['title' => ''])

@extends('layouts.app')
@section('title', $title)

@section('content')
    {{ $slot }}
@endsection 