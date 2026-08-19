<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\EventType;
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

         $title= request()->title;
    $description= request()->description;
   
    $image= request()->image;
    $start_date= request()->start_date;
    $end_date= request()->end_date;
    $event_type_id= request()->event_type_id;
        // $post= new Post;
        // $post->title = $title;
        // $post->description = $description;
        // // $post->post_creator = $post_creator;
        // $post->save();
        Post::create([
            'title'=>$title,
            'description'=>$description,
            'image'=>$image,
            'start_date'=>$start_date,
            'end_date'=>$end_date,
            'event_type_id'=>$event_type_id,
        ]
   );
        // return redirect()
        //     ->route("event.index")
        //     ->with('success', __('تم إدخال الحدث بنجاح'));
            return redirect()->route('admin.event.index')->with('success', 'تم إدخال الحدث بنجاح');
    }


}
