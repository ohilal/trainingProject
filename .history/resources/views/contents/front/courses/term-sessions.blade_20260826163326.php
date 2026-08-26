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

        <div class="row g-4">
            @forelse ($term->Sessions as $session)
                <div class="col-lg-6">
                    <div class="service-item d-flex align-items-center rounded p-4 h-100">
                        <div class="service-icon flex-shrink-0 me-3">
                            <i class="fa fa-book fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="mb-2">{{ $session->title }}</h5>

                            @foreach ($session->Files as $file)
                             asset('storage/files/events/' . $file->file) }}"
                                <a href="{{ asset($file->file) }}" target="_blank" rel="noopener">
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
                {{ __('Back to terms') }}
            </a>
        </div>
    </div>
</div>
@endsection
