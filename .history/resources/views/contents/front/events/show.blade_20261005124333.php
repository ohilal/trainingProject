@extends('layouts.front.theme')


@section("content")
<div class="space"></div>
<div class="container-xxl py-5">
    <div class="container py-5 px-lg-5">
        <div class="wow fadeInUp" data-wow-delay="0.1s">
            <p class=" text-secondary justify-content-center text-nowrap text-center"><span> الأنشطة والفاعاليات</span></p>
            <h1 class="text-center mb-5">  حفلات تكريم </h1>
        </div>
  <div class="card mx-auto col-lg-6 col-md-8 col-sm-10 border-0">
    <!-- <img class="card-img-top img-responsive" src="/course/{{$event->image}}" alt="Card image" > -->
 @if($event->image)
 <img src="{{ asset('storage/files/events/' . $event->image) }}"  alt="{{ $event->title }}" style="max-width: 500px; height: auto;" >

        @else
            <img src="{{ asset('/course/file.png') }}" alt="No Image">
        @endif
    <div class="card-body ">
            <div class="card-title h3 text-center" data-filter="{{ $event['id'] }}">{{ $event ->title}} </div>
           <i class="fa fa-calendar-alt text-muted"> </i> <small class="text-muted"> {{ $event['start_date'] }}   </small>

      <p class="card-text">
<!-- this is to show the HTML editor data -->
        {!! $event->description !!}
        
      </p>
      <a href="{{ Storage::url($image->image) }}" 
   data-lightbox="event-gallery" 
   data-title="{{ $image->title ?? 'Event Image' }}" 
   class="gallery-item">
    <img src="{{ Storage::url($image->path) }}" 
         alt="{{ $image->title ?? 'Event Image' }}" 
         class="img-fluid rounded shadow-sm" 
         style="width: 150px; height: 150px; object-fit: cover;">
</a>
    </div>
</div>
  </div>
 </div>

@endsection