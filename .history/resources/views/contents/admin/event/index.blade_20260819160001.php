
@extends('layouts.admin')


@section("content")
<table class="table table-reponsive table-striped">
    <thead>
      <tr>
        <th>العنوان</th>
        <th>تاريخ الحدث</th>
        <th>الصورة الرئيسية</th>
      </tr>
    </thead>
 <tbody> 
    @foreach ($events as $event)
      <tr>
        <td>{{ $event['id'] }}">{{ $event['title'] }} </td>
        <td>{{ $event['start_date'] }} </td>
        <td><img src="/course/{{$event->image}}" alt="Main Image" style="max-width: 100px; max-height: 100px;"></td>
      </tr>           
            @endforeach
      </tbody>
</table>
  @foreach ($events as $event)
            
<div class="col">
  <div class="card ">
    <img class="card-img-top img-responsive" src="/course/file.png" alt="Card image" >

    <div class="card-body ">
            <div class="card-title h3" data-filter="{{ $event['id'] }}">{{ $event['title'] }} </div>
           <i class="fa fa-calendar-alt text-muted"> </i> <small class="text-muted"> {{ $event['start_date'] }}   </small>

      <p class="card-text">
        <!-- {{ $event['description'] }} -->
        <!-- {{ Illuminate\Support\Str::limit(strip_tags($event['description']), 100, '...') }} -->
        {{ Illuminate\Support\Str::words(strip_tags($event['description']), 20, '...') }}
       
      </p>
    </div>
    
  </div>
</div>
 @endforeach           
     
@endsection