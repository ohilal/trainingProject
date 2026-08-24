@extends('layouts.front.theme')


@section("content")
<div class="space"></div>


<!-- Projects Start -->
<!-- <div class="container-xxl py-5">
    <div class="container py-5 px-lg-5">
        <div class="wow fadeInUp" data-wow-delay="0.1s">

            <h1 class="text-center mb-5">دورات  {{ $department->title }} </h1>
        </div>
        <div class="row mt-n2 wow fadeInUp" data-wow-delay="0.3s">
            <div class="col-12 text-center">
                <ul class="list-inline mb-5" id="portfolio-flters">
                    <li class="mx-2 active" data-filter="*">كل الدورات ALL</li>
                    @forelse ($departments as $department)
                        <li class="mx-2" data-filter=".department-{{ $department->id }}">{{ $department->title }}</li>
                    @empty
                    @endforelse
                </ul>
            </div>
        </div>
        <div class="row g-3 portfolio-container">-->
            <!-- course Start -->
                <!-- @if(count($courses) > 1)
                <div class="container-xxl py-5">
                    <div class="container py-5 px-lg-5">
                        <div class="row g-4">
                            @forelse ($courses as $course)
                                <x-front.course :course="$course"/>
                                 
                            @empty
                            @endforelse
                        </div>
                    </div>
                </div>
                @endif
            course End -->
        <!-- </div>
    </div>
</div>  -->
<h1>Courses in {{ $department->title }}</h1>

<div class="row g-3 portfolio-container">
    @forelse($courses as $course)
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">{{ $course->title }}</h5>
                    <p class="card-text">{{ Str::limit($course->description, 100) }}</p>
                    <a href="{{ route('course.show', $course->id) }}" class="btn btn-primary">View Details</a>
                </div>
            </div>
        </div>
    @empty
        <p>No courses found for this department.</p>
    @endforelse
</div>
<!-- Projects End -->

@endsection