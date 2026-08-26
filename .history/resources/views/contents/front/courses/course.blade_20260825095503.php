@extends('layouts.front.theme')


@section("content")
<div class="space"></div>
<!-- Terms Start -->
<div class="container-xxl py-5">
    <div class="container py-5 px-lg-5">
        <div class="wow fadeInUp" data-wow-delay="0.1s">
            <p class="section-title text-secondary justify-content-center"><span></span>
                {{ __('Terms') }}
                <span></span></p>
            <h1 class="text-center mb-5">What Solutions We Provide</h1>
        </div>
        <div class="row g-4">
            @forelse ($course->Terms as $term)
            <x-front.term :term="$term" :iteration="$loop->iteration"/>
            @empty
            @endforelse
        </div>
        <div class="row g-4 mt-5">
            <div class="h3 text-center">تفاصيل الدورة</div>
            <div class="card-body">
                <h5 class="card-title">{{ $course->title }}</h5>
                  <img class="img-fluid w-80"  src="{{ URL::to('course/' . $course->image)  ?: URL::to('front/img/portfolio-1.jpg') }}" alt="" >
                <p class="card-text">{!! $course->description !!}</p>
                <a href="{{ $course->link }}" class="btn btn-primary" target="_blank">رابط الدورة</a>
            
            </div>
        </div>
    </div>
</div>
<!-- Terms End -->


@endsection