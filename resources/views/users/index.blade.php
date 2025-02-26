@extends('layouts.app')

@section('title', 'Użytkownicy')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Użytkownicy</h1>
                </div>
                <div class="col-sm-6">
                    <div class="float-sm-right">
                        @can('users.create')
                            <a href="{{ route('users.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Dodaj użytkownika
                            </a>
                        @endcan
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content">
        <div class="container-fluid">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            @endif

            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nazwa</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Ostatnie logowanie</th>
                                    <th>Akcje</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td>{{ $user->id }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            @foreach($user->roles as $role)
                                                <span class="badge badge-info">{{ $role->display_name }}</span>
                                            @endforeach
                                        </td>
                                        <td>
                                            @if($user->is_active)
                                                <span class="badge badge-success">Aktywny</span>
                                            @else
                                                <span class="badge badge-danger">Nieaktywny</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($user->last_login_at)
                                                {{ $user->last_login_at->format('d.m.Y H:i') }}
                                            @else
                                                Nigdy
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                @can('users.view')
                                                    <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-info" title="Szczegóły">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                @endcan
                                                
                                                @can('users.edit')
                                                    <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-primary" title="Edytuj">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endcan
                                                
                                                @can('users.delete')
                                                    @if($user->id !== auth()->id())
                                                        <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger" title="Usuń" 
                                                                    onclick="return confirm('Czy na pewno chcesz usunąć tego użytkownika?')">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</div>
@endsection 