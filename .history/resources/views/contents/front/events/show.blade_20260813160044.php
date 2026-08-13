@extends('layouts.front.theme')


@section("content")
<div class="space"></div>
  <div class="card mx-auto">
    <img class="card-img-top img-responsive" src="/course/file.png" alt="Card image" >

    <div class="card-body ">
            <div class="card-title h3" data-filter="{{ $event['id'] }}">{{ $event['title'] }} </div>
           <i class="fa fa-calendar-alt text-muted"> </i> <small class="text-muted"> {{ $event['start_date'] }}   </small>

      <p class="card-text">

        {{ $event['description']}}
       
      </p>
    </div>
    <div class="card-footer"><a href="/front/events/{{ $event['id'] }}" class="btn btn-darkteal float-end">المزيد..</a>
</div>
  </div>
@endsection