<div class="text-warning">
    @for($star = 1; $star <= 5; $star++)
        @if($rating >= $star)
            <i class="fa fa-star"></i>
        @elseif($rating >= $star - 0.5)
            <i class="fa fa-star-half-alt"></i>
        @else
            <i class="fa fa-star text-secondary"></i>
        @endif
    @endfor
</div>
