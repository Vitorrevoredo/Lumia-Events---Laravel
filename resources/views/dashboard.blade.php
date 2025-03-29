@extends('layouts.main')

@section('title', 'Dashboard')

@section('content')

<div class="col-md-10 offset-md-1 dashboard-title-container">
    <h1>Meus eventos</h1>
</div>
<div class="col-md-10 offset-md-1 dashboard-events-container">
    @if(count($events) > 0)
    @else
        <p class="no-events">Você ainda não tem eventos criados, <a href="/events/create">crie um evento</a></p>
    @endif
</div>

@endsection