@extends('layouts.app')

@section('content')
    <h1>Dodaj nowy projekt</h1>

    <div style="margin-bottom:20px;">
        <a href="{{ route('admin.projects.index') }}">Powrót do Listy Projektów</a>
    </div>

    @if($errors->any())
        <div style="color:red;">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.projects.store') }}">
        @csrf
        <div>
            <label for="name">Nazwa projektu:</label>
            <input type="text" name="name" id="name" value="{{ old('name') }}" required>
        </div>

        <div>
            <label for="description">Opis projektu:</label>
            <textarea name="description" id="description">{{ old('description') }}</textarea>
        </div>

        <div style="margin-top:10px;">
            <button type="submit">Utwórz projekt</button>
        </div>
    </form>
@endsection
