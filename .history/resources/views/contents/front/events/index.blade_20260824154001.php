@extends('layouts.front.theme')


@section("content")
<div class="space"></div>


<!-- events Start -->
<div class="container-xxl py-5">
    <div class="container py-5 px-lg-5">
        <div class="wow fadeInUp" data-wow-delay="0.1s">
            <p class="section-title text-secondary justify-content-center text-nowrap"><span> الأنشطة والفاعاليات</span></p>
            <h1 class="text-center mb-5">  حفلات تكريم </h1>
        </div>
        <!----------------------------------->
        <div class="row row-cols-1 row-cols-md-3 g-4">
            @foreach ($events as $event)
            
<div class="col">
  <div class="card ">
    <!-- <img class="card-img-top img-responsive" src="/course/file.png" alt="Card image" > -->
      @if($event->image)

      <img src="{{ asset('storage/files/events/' . $event->image) }}"   alt="{{ $event->title }}" style="max-width: 500px; height: auto;" >

        @else
            <img src="{{ asset('/course/file.png') }}" alt="No Image">
        @endif

    <div class="card-body ">
            <div class="card-title h3" data-filter="{{ $event['id'] }}">{{ $event['title'] }} </div>
           <i class="fa fa-calendar-alt text-muted"> </i> <small class="text-muted"> {{ $event['start_date'] }}   </small>

      <p class="card-text">
        <!-- {{ $event['description'] }} -->
        <!-- {{ Illuminate\Support\Str::limit(strip_tags($event['description']), 100, '...') }} -->
        {{ Illuminate\Support\Str::words(strip_tags($event['description']), 20, '...') }}
       
      </p>
    </div>
    <div class="card-footer"><a href="/front/events/{{ $event['id'] }}" class="btn btn-darkteal float-end">المزيد..</a>
  
</div>
  </div>
</div>
            
                
            @endforeach
              

        <!----------------------------------->


    </div>
</div>
<!-- Projects End -->

@endsection