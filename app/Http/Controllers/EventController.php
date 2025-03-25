<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Event;

class EventController extends Controller
{

    public function index(){

        $events = Event::all();
    
        return view('welcome',['events' => $events]);
        
    }

    public function create (){
        return view('events.create');
    }

    public function store(Request $request){
        $event = new Event;
        $event->title= $request->title;
        $event->date = $request->date;
        $event->city = $request->city;
        $event->private = $request->private;
        $event->description = $request->description;
        $event->items = $request->items;

        // Image upload
        if($request->hasFile('image') && $request->file('image')->isValid()){
            $requestImage = $request->image;
            $extension = $requestImage->extension();
            $imageName = md5($requestImage->getClientOriginalName() . strtotime("now")) . "." . $extension;
            $requestImage->move(public_path('img/events'), $imageName);
            $event->image = $imageName;
        }
        
        $event->save();

        return redirect('/')->with('msg','Evento criado com sucesso!');
    }

    public function show($id){
        $event = Event::findOrFail($id);

        return view('events.show',['event' => $event]);
    }
    
    public function edit($id){
        $event = Event::findOrFail($id);

        return view('events.edit',['event' => $event]);
    }

    public function update(Request $request){
        $data = $request->all();

        // Image upload
        if($request->hasFile('image') && $request->file('image')->isValid()){
            $requestImage = $request->image;
            $extension = $requestImage->extension();
            $imageName = md5($requestImage->getClientOriginalName() . strtotime("now")) . "." . $extension;
            $requestImage->move(public_path('img/events'), $imageName);
            $data['image'] = $imageName;
        }

        Event::findOrFail($request->id)->update($data);

        return redirect('/')->with('msg','Evento editado com sucesso!');
    }
    
    public function destroy($id){
        Event::findOrFail($id)->delete();

        return redirect('/')->with('msg','Evento deletado com sucesso!');
    }

}
