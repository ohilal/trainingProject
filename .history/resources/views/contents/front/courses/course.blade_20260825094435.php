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
        <div class="card">
            <div class="card-header">تفاصيل الدورة</div>
            <div class="card-body">
                <h5 class="card-title">{{ $course->title }}</h5>
                <p class="card-text">{!! $course->description !!}</p>
                
            </div>
        </div>
    </div>
</div>
<!-- Terms End -->


@endsection