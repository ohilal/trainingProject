<div class="text-center">
    <h4>{{ __('الوصف: ') }} {{ $file->description }}</h4>
    <hr/>
    <object data="{{ $url }}" type="application/pdf" width="100%" height="100%" style="width:100%, height:90vh;">
        <p>Alternative text - include a link <a href="{{ $url }}">to the PDF!</a></p>
    </object>

</div>