@extends('layouts.main')

@section('title', 'Editando ' . $event->title)

@section('content')

<div id="event-create-container" class="col-md-6 offset-md-3">
    <h1>Editando: {{ $event->title }}</h1>
    <form action="/events/update/{{ $event->id }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="image">Imagem do evento:</label>
            <input type="file" id="image" name="image" class="form-control-file">
            <img src="/img/events/{{ $event->image }}" alt="{{ $event->title }}" class="img-preview">
        </div>

        <div class="form-group">
            <label for="title">Evento:</label>
            <input type="text" class="form-control" id="title" name="title" placeholder="Nome do evento" value="{{ $event->title }}">
        </div>

        <div class="form-group">
            <label for="date">Data do evento:</label>
            <input type="date" class="form-control" id="date" name="date" value="{{ \Carbon\Carbon::parse($event->date)->format('Y-m-d') }}">
            </div>

        <div class="form-group">
            <label for="city">Cidade:</label>
            <input type="text" class="form-control" id="city" name="city" placeholder="Local do evento" value="{{ $event->city }}">
        </div>

        <div class="form-group">
            <label for="private">O evento é privado?</label>
            <select name="private" id="private" class="form-control">
                <option value="0" {{ !$event->private ? 'selected' : '' }}>Não</option>
                <option value="1" {{ $event->private ? 'selected' : '' }}>Sim</option>
            </select>
        </div>

        <div class="form-group">
            <label for="description">Descrição:</label>
            <textarea name="description" id="description" class="form-control" placeholder="O que vai acontecer no evento?">{{ $event->description }}</textarea>
        </div>

        <div class="form-group">
            <label>Adicione itens de infraestrutura</label>
            @php
                $eventItems = $event->items ? json_decode($event->items, true) : [];
            @endphp
            @foreach(['Cadeiras', 'Palco', 'Cerveja grátis', 'Open Food', 'Brindes'] as $item)
                <div class="form-group">
                    <input type="checkbox" name="items[]" value="{{ $item }}" {{ in_array($item, $eventItems) ? 'checked' : '' }}> {{ $item }}
                </div>
            @endforeach
        </div>

        <input type="submit" value="Editar Evento" class="btn btn-primary">
    </form>
</div>

@endsection
