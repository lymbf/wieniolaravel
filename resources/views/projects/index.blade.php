@extends('layouts.app')

@section('content')
    <h1>Twoje projekty</h1>
    
    @if($projects->isEmpty())
        <p>Nie masz jeszcze żadnych projektów.</p>
    @else
        <ul>
            @foreach($projects as $project)
                <li>
                    <a href="{{ route('projects.show', $project->id) }}">{{ $project->name }}</a>
                </li>
            @endforeach
        </ul>
    @endif

    @can('access-admin-panel')
        <div style="margin-top:20px;">
            <a href="{{ route('admin.dashboard') }}">Przejdź do panelu administracyjnego</a>
        </div>
    @endcan
@endsection
