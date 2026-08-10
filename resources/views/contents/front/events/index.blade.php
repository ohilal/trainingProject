@extends('layouts.front.theme')


@section("content")
<div class="space"></div>


<!-- Projects Start -->
<div class="container-xxl py-5">
    <div class="container py-5 px-lg-5">
        <div class="wow fadeInUp" data-wow-delay="0.1s">
            <p class="section-title text-secondary justify-content-center"><span> الأنشطة والفاعاليات</span><span></span></p>
            <h1 class="text-center mb-5">  حفلات تكريم </h1>
        </div>
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
                @if(count($events) > 1)
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
                @endif
            <!-- course End -->
        </div>
    </div>
</div>
<!-- Projects End -->

@endsection