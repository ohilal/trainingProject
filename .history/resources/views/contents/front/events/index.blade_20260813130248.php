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
    <img class="card-img-top img-responsive" src="/course/file.png" alt="Card image" >

    <div class="card-body ">
            <div class="card-title h2" data-filter="{{ $event['id'] }}">{{ $event['title'] }} </div>
           <i class="fa fa-calendar-alt text-muted"> </i> <small class="text-muted"> {{ $event['start_date'] }}   </small>

      <p class="card-text">
        {{ $event['description'] }}
        
       
      </p>
    </div>
    <div class="card-footer"><a href="#" class="btn btn-darkteal float-end">المزيد..</a>
</div>
  </div>
</div>
            
                
            @endforeach
              
<!---------------------------------->  
<div class="col">
  <div class="card ">
    <img class="card-img-top img-responsive" src="/course/file.png" alt="Card image" >

    <div class="card-body text-center">
            <div class="card-title">Header</div>
      <p class="card-text">Some text inside the first card</p>
    </div>
  </div></div>
  <!---------------------------------->
<div class="col">
  <div class="card ">
    <img class="card-img-top img-responsive" src="/course/file.png" alt="Card image" >

    <div class="card-body text-center">
            <div class="card-title">Header</div>
      <p class="card-text">Some text inside the first card</p>
    </div>
  </div></div>
</div>
        <!----------------------------------->
        <div class="row mt-n2 wow fadeInUp" data-wow-delay="0.3s">
            <div class="col-12 text-center">
                <ul class="list-inline mb-5" id="portfolio-flters">
                    <li class="mx-2 active" data-filter="*">All</li>
                    @forelse ($events as $event)
                        <li class="mx-2" data-filter=".event-{{ $event->id }}">{{ $event->title }}</li>
                    @empty
                    @endforelse
                </ul>
            </div>
        </div>
        <div class="row g-3 portfolio-container">
            <!-- course Start -->
                {{-- @if(count($events) > 1)
                <div class="container-xxl py-5">
                    <div class="container py-5 px-lg-5">
                        <div class="row g-4">
                            @forelse ($events as $event)
                                <x-front.event :event="$event"/>
                            @empty
                            @endforelse
                        </div>
                    </div>
                </div>
                @endif --}}
            <!-- course End -->
        </div>
    </div>
</div>
<!-- Projects End -->

@endsection