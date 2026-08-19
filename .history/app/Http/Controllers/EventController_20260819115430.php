<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use Illuminate\View\View;

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
   public function show($id)
{
    $event = Event::findOrFail($id); // Variable name updated to match
    
    return view('contents.front.events.show', compact('event')); // Works perfectly
}
public function create()
{
    //return ('We are in the create method of EventController');
     return view('contents.admin.event.create');
}

     public function store(Request $request)
    {
        $data = $request->all();
        $title= $data['title'];
    }
    

}
