<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\EventType;
use Illuminate\View\View;
use App\Http\Requests\EventRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class EventController extends Controller
{
    public function index()
    {
        
        $events = Event::orderBy('start_date','desc')->paginate(10);
        return view('contents.front.events.index', ['events' => $events]);

    }
    // public function show(Event $event) //type-hinting -- route model binding
    // {
    //    //$event = Event::findOrFail($event->id);
    //     return view('contents.front.events.show', ['event' => $event]);
    // }
    public function adminIndex()
{
    $events = Event::all();
    return view('contents.admin.event.index', compact('events'));
}
   public function show($id)
{
    $event = Event::findOrFail($id); // Variable name updated to match
    
    return view('contents.front.events.show', compact('event')); // Works perfectly
}

  public function create()
    {
        // Fetch all event types to pass to the view
    $eventTypes = EventType::all(); 
      $this->authorize('event.create');
    return view('contents.admin.event.create', compact('eventTypes'));
      
       // return view('contents.admin.event.create');
    }

  public function edit($id)
    {
        $event = Event::findOrFail($id);
        $eventTypes = EventType::all(); // Fetch all event types to pass to the view
        $this->authorize('event.edit', $event);
        return view('contents.admin.event.edit', compact('event', 'eventTypes'));
    }
    public function update(EventRequest $request, $id)
    
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


 $validated = $request->validated();

    if ($request->hasFile('image')) {
        $file = $request->file('image');
        
        // Generate a unique name to prevent overwriting
        $imageName = Str::random(40) . '.' . $file->getClientOriginalExtension();
        
        // Store the file in storage/app/public/files/events
        //$file->move(storage_path('app/public/files/events'), $imageName);
     
        // Save only the filename to the database
        //$validated['image'] = $imageName;
        ///////////////////////
            
        // Define the destination path explicitly
        $destinationPath = storage_path('app/public/files/events');
        
        // Create the directory if it doesn't exist
        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true, true);
        }
        
        // Move the file physically
        $file->move($destinationPath, $imageName);
        
        // Save only the filename to the database
        $validated['image'] = $imageName;
    }

    Event::create($validated);
            return redirect()->route('admin.event.index')->with('success', 'تم إدخال الحدث بنجاح');
    }


}
