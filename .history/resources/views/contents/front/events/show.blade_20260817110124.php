@extends('layouts.front.theme')


@section("content")
<div class="space"></div>
  <div class="card mx-auto col-lg-6 col-md-8 col-sm-10">
    <img class="card-img-top img-responsive" src="/course/file.png" alt="Card image" >

    <div class="card-body ">
            <div class="card-title h3" data-filter="{{ $event['id'] }}">{{ $event['id'] }} - {{ $event ->title}} </div>
           <i class="fa fa-calendar-alt text-muted"> </i> <small class="text-muted"> {{ $event['start_date'] }}   </small>

      <p class="card-text">
        {{ $event['description']}}
      </p>
    </div>
</div>
  </div>
  <p>{{ $event->id }}</p>
  <h1>{{ $event->title }}</h1>
<p>{{ $event->description }}</p>
<small>Created on: {{ $event->start_date }}</small>
@endsection