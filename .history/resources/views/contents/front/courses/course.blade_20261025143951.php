@extends('layouts.front.theme')


@section("content")
<div class="space"></div>
<!-- Terms Start -->
<div class="container-xxl py-5">
    <div class="container py-5 px-lg-5">
               <div class="wow fadeInUp" data-wow-delay="0.1s">
                    <h3 class="section-title text-secondary justify-content-center mt-2"><span></span>
                {{ __('تفاصيل الدورة') }}
                <span></span></h3> 
         
          
           
             <div class="row col-8 mx-auto border rounded">
            <div class="col-lg-4 m-0 p-0">
                
                  <img class="img-fluid w-80 "  src="{{ URL::to('course/' . $course->image)  ?: URL::to('front/img/portfolio-1.jpg') }}" alt="" >
               </div>
                  <div class="col-lg-8 align-self-center">
                     <h5 class="h3  text-darkteal">{{ $course->title }}</h5>
                 {!! $course->description !!}
                     {!! $event->description !!}
                <!-- <a href="{{ $course->link }}" class="btn btn-darkteal flex-end" target="_blank">رابط الدورة</a> -->
            
            
        </div>
        </div>
        @if($course->allow_self_enrollment)
            @auth
                <div class="text-center mt-4">
                    <form method="POST" action="{{ route('front.course.join', $course->id) }}">
                        @csrf
                        <button type="submit" class="btn btn-darkteal rounded">{{ __('اشترك فى هذه الدورة') }}</button>
                    </form>
                </div>
            @else
                <p class="text-center mt-4">
                    <a href="{{ route('login') }}">{{ __('برجاء تسجيل الدخول للاشتراك في هذه الدورة.') }}</a>
                </p>
            @endauth
        @else
            <p class="text-center mt-4">{{ __('يجب أن يتم تعيين الدارسين إلى هذه الدورة بواسطة المسؤول.') }}</p>
        @endif
        </div>
        <div class="wow fadeInUp" data-wow-delay="0.3s mt-2">
            <p class="section-title text-secondary justify-content-center"><span></span>
                {{ __('أقسام الدورة التدريبية') }}
                <span></span></p>
            <!-- <h1 class="text-center mb-5">الحلول التي نقدمها
               ()
            </h1> -->
        </div>
        <div class="row g-4 mx-auto justify-content-center">
            @forelse ($course->Terms as $term)
            <x-front.term :term="$term" :iteration="$loop->iteration"/>
            @empty
            @endforelse
        </div>

    </div>
</div>
<!-- Terms End -->


@endsection