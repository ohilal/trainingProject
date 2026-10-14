@extends('layouts.front.theme')

@section('content')
<div class="space"></div>

<div class="container-xxl py-5">
    <div class="container py-5 px-lg-5">
        <div class="text-center mb-5">
            <p class="section-title text-secondary justify-content-center">
                <span></span>{{ $course->title }}<span></span>
            </p>
            <h1>{{ $term->title }}</h1>
        </div>

        <div class="row g-4 justify-content-center">
            @forelse ($term->Sessions as $session)
                <div class="col-lg-4 col-md-6">
                    <div class="service-item d-flex flex-column text-center rounded h-100">
                        <div class="service-icon flex-shrink-0">
                            <i class="fa fa-book fa-2x"></i>
                        </div>
                        <h5 class="mb-3">{{ $session->title }}</h5>
                        <div class="d-flex flex-column gap-2 text-start">
                            @foreach ($session->Files as $file)
                                <a href="{{ asset('storage/' . $file->file) }}" target="_blank" rel="noopener" class="white text-decoration-none">
                                    <i class="fa fa-file me-1"></i>{{ $file->title }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center">
                    <p class="text-muted">{{ __('No sessions available for this term.') }}</p>
                </div>
            @endforelse
        </div>

        <div class="text-center mt-5">
            <a href="{{ route('front.course', $course->id) }}" class="btn btn-darkteal">
                {{ __('عودة إلى صفحة الأقسام') }}
            </a>
        </div>
    </div>
</div>
@endsection
