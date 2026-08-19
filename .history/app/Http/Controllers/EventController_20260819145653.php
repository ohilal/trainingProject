<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use Illuminate\View\View;
use App\Http\Requests\EventRequest;

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
        $this->authorize('event.create');
        return view('contents.admin.event.create');
    }

    //  public function store(Request $request)
    // {
    //     $data = $request->all();
    //     $title= $data['title'];
    // }
      /**
     * Store a newly created resource in storage.
     *
     * @param  EventRequest $request
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function store(EventRequest $request)
    {
        $this->authorize('event.create');
        Event::create($request->all());
        // return redirect()
        //     ->route("event.index")
        //     ->with('success', __('تم إدخال الحدث بنجاح'));
            return redirect()->route('admin.event.index')->with('success', 'تم إدخال الحدث بنجاح');
    }

}
