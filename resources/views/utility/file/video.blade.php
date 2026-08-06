<div class="text-center">
    <h3 class="p-4">{{ __('description: ') }} {{ $file->description }}</h3>
    <hr/>
    <div class="embed-responsive embed-responsive-16by9">
        <iframe class="embed-responsive-item p-4" src="{{ $url }}" allowfullscreen>
        </iframe>
        <video class="w-100 p-4" controls>
    <source src="{{ $url }}" type="video/mp4">
    {{ __('Your browser does not support the video tag.') }}
</video>
    </div>
</div>