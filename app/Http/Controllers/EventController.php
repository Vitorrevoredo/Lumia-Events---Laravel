<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class EventController extends Controller
{
    public function index()
    {
        $search = request('search');

        if ($search) {
            $events = Event::where('title', 'like', '%' . $search . '%')->get();
        } else {
            $events = Event::all();
        }

        return view('welcome', ['events' => $events, 'search' => $search]);
    }

    public function create()
    {
        return view('events.create');
    }

    public function store(Request $request)
    {
        $event = new Event;
        $event->title = $request->title;
        $event->date = $request->date;
        $event->city = $request->city;
        $event->private = $request->private;
        $event->description = $request->description;
        $event->items = json_encode($request->items);

        // Upload de imagem
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $image = $request->image;
            $imageName = md5($image->getClientOriginalName() . time()) . '.' . $image->extension();
            $image->move(public_path('img/events'), $imageName);
            $event->image = $imageName;
        }

        $event->user_id = Auth::id();
        $event->save();

        return redirect('/')->with('msg', 'Evento criado com sucesso!');
    }

    public function show($id) {
        $event = Event::findOrFail($id);
        $eventOwner = User::where('id', $event->user_id)->first();
    
        // Converte items de JSON string para array
        $event->items = json_decode($event->items, true);
    
        return view('events.show', ['event' => $event, 'eventOwner' => $eventOwner]);
    }
    

    public function dashboard()
    {
        $events = Auth::user()->events;

        return view('events.dashboard', ['events' => $events]);
    }

    public function destroy($id)
    {
        $event = Event::findOrFail($id);

        if (Auth::id() !== $event->user_id) {
            return redirect('/dashboard')->with('msg', 'Você não tem permissão para deletar este evento.');
        }

        $event->delete();

        return redirect('/dashboard')->with('msg', 'Evento deletado com sucesso!');
    }

    public function edit($id)
    {
        $event = Event::findOrFail($id);

        if (Auth::id() !== $event->user_id) {
            return redirect('/dashboard')->with('msg', 'Você não tem permissão para editar este evento.');
        }

        return view('events.edit', ['event' => $event]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'city' => 'required|string|max:255',
            'private' => 'required|boolean',
            'description' => 'nullable|string',
            'items' => 'nullable|array',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $event = Event::findOrFail($request->id);

        if (Auth::id() !== $event->user_id) {
            return redirect('/dashboard')->with('msg', 'Você não tem permissão para editar este evento.');
        }

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = md5($image->getClientOriginalName() . time()) . '.' . $image->extension();
            $image->move(public_path('img/events'), $imageName);
            $data['image'] = $imageName;
        }

        $data['items'] = json_encode($request->items);

        $event->update($data);

        return redirect('/dashboard')->with('msg', 'Evento editado com sucesso!');
    }

    public function joinEvent($id)
    {
        $user = Auth::user();
        $event = Event::findOrFail($id);

        if ($user->id == $event->user_id) {
            return redirect('/dashboard')->with('msg', 'Você não pode participar do seu próprio evento.');
        }

        if ($event->users()->where('user_id', $user->id)->exists()) {
            return redirect('/dashboard')->with('msg', 'Você já está participando deste evento.');
        }

        $event->users()->attach($user);

        return redirect('/dashboard')->with('msg', 'Você se inscreveu no evento com sucesso!');
    }
}
