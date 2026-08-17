<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;

class EventController extends Controller
{
    public function index()
    {
        
        $events = Event::orderBy('created_at','desc')->paginate(10);
        return view('contents.front.events.index', ['events' => $events]);

    }
    // public function show(Event $event) //type-hinting -- route model binding
    // {
    //    //$event = Event::findOrFail($event->id);
    //     return view('contents.front.events.show', ['event' => $event]);
    // }
   
     public function show(string $id): View
    {
        return view('contents.front.events.show', [
            'event' => Event::findOrFail($id)
        ]);
    }
     public function store(Request $request)
    {
        $data = $request->all();
        $title= $data['title'];
    }
    

}
