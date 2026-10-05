<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\EventType;
use App\Models\EventImage;
use Illuminate\View\View;
use App\Http\Requests\EventRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class EventController extends Controller
{
     protected $service;
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


        //$event = Event::findOrFail($id);
          $event = Event::with('images')->findOrFail($id);
        $eventTypes = EventType::all(); // Fetch all event types to pass to the view
        $this->authorize('event.edit');
        return view('contents.admin.event.create', compact('event', 'eventTypes'));
    }
    //  public function edit($id)
    // {
    //     $this->authorize('event.edit');
    //     $event = $this->service->findById($id);
    //      $eventTypes = EventType::all();
    //     return $this->service->view('contents.admin.event.create', compact('event','eventTypes'));
    // }

    public function update(EventRequest $request, $id)
    {
    //$event = Event::findOrFail($id);
      $event = Event::with('images')->findOrFail($id);
    $this->authorize('event.edit');

    $validated = $request->validated();

    // Handle Image Upload
    if ($request->hasFile('image')) {
        // Delete old image if exists
        if ($event->image && File::exists(storage_path('app/public/files/events/' . $event->image))) {
            File::delete(storage_path('app/public/files/events/' . $event->image));
        }
        
        $file = $request->file('image');
        $imageName = Str::random(40) . '.' . $file->getClientOriginalExtension();
        $file->move(storage_path('app/public/files/events'), $imageName);
        $validated['image'] = $imageName;
    }

    // Update the event
    $event->update($validated);

    return redirect()->route('admin.event.index')->with('success', 'تم تحديث الفعالية بنجاح  ');

   
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
      // Save gallery images (lightbox)
	        $lastEvent = Event::latest('id')->first();

         if ($request->hasFile('gallery')) {

  	            $this->saveGalleryImages($request->file('gallery'), $lastEvent->id);

  	        }
            return redirect()->route('admin.event.index')->with('success', 'تم إدخال الحدث بنجاح');
    }
    protected function saveGalleryImages(array $files, int $eventId): void
22
   162	    {
23
   163	        $destinationPath = storage_path('app/public/files/events');
24
   164	        if (!File::exists($destinationPath)) {
25
   165	            File::makeDirectory($destinationPath, 0755, true, true);
26
   166	        }
27
   167	
28
   168	        foreach ($files as $file) {
29
   169	            $imageName = Str::random(40) . '.' . $file->getClientOriginalExtension();
30
   170	            $file->move($destinationPath, $imageName);
31
   171	            EventImage::create([
32
   172	                'event_id' => $eventId,
33
   173	                'image'    => $imageName,
            ]);
	        }
	    }
 public function destroy(int $id)
    {
        $this->authorize('event.delete');
       $event = Event::findOrFail($id);
       
        if ($event::destroy($id) ){ 
           return redirect()->route('admin.event.index')->with('danger', 'تم حذف الحدث !!');
            ;}
           
    }

}
 

  #  return view('contents.front.events.show', compact('event')); // Works perfectly