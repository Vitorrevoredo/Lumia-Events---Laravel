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

        $events = $search ? 
            Event::where('title', 'like', '%' . $search . '%')->get() : 
            Event::all();

        return view('welcome', compact('events', 'search'));
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
        $event->items = json_encode($request->items ?? []); // Garante que não seja null

        // Upload de imagem
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            $image = $request->file('image');
            $imageName = md5($image->getClientOriginalName() . time()) . '.' . $image->extension();
            $image->move(public_path('img/events'), $imageName);
            $event->image = $imageName;
        }

        $event->user_id = Auth::id();
        $event->save();

        return redirect('/')->with('msg', 'Evento criado com sucesso!');
    }

    public function show($id)
    {
        $event = Event::findOrFail($id);
        $eventOwner = User::find($event->user_id);

        // Garante que items sempre será um array
        $event->items = json_decode($event->items ?? '[]', true);

        return view('events.show', compact('event', 'eventOwner'));
    }

    public function dashboard()
    {
        $user = Auth::user();
        $events = $user->events;
        $eventsAsParticipant = $user->eventsAsParticipant;

        return view('events.dashboard', compact('events', 'eventsAsParticipant'));
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

        return view('events.edit', compact('event'));
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

        $data['items'] = json_encode($request->items ?? []);

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

        $event->users()->attach($user->id);

        return redirect('/dashboard')->with('msg', 'Você se inscreveu no evento com sucesso!');
    }
    public function leaveEvent($id) {
        
        $user = Auth::user();
        $event = Event::findOrFail($id);

        if ($user->id == $event->user_id) {
            return redirect('/dashboard')->with('msg', 'Você não pode sair do seu próprio evento.');
        }

        if (!$event->users()->where('user_id', $user->id)->exists()) {
            return redirect('/dashboard')->with('msg', 'Você não está participando deste evento.');
        }

        $event->users()->detach($user->id);

        return redirect('/dashboard')->with('msg', 'Você saiu do evento com sucesso!');
    }
}
