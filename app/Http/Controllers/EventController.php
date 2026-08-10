<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        return view('contents.front.events.index');
    }
    public function show(Event $event) //type-hinting -- route model binding
    {
        return view('contents.front.events.show', ['event' => $event]);
    }
     public function store(Request $request)
    {
        $data = $request->all();
        $title= $data['title'];
    }
}
