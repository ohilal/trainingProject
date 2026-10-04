
<div class="col-lg-2 wow fadeInUp" data-wow-delay="0.1s" style="display: flex; flex-direction: column;">
    <div class="feature-item bg-light rounded text-center p-4" style="display: flex; flex-direction: column; height: 100%;">
        <i class="fas fa-3x {{ $department->icon }} text-darkteal mb-4"></i>
        <h5 class="mb-3 text-darkteal">{{ $department->title }}</h5>
        <p class="m-0">{{ Str::words($department->description, 30, '...') }}</p>
    </div>
</div>
