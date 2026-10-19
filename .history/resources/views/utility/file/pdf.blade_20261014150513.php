<div class="text-center">
    <h4>{{ __('وصف المادة العلمية: ') }} {{ $file->description }}</h4>
    <hr/>
    <object data="{{ $url }}" type="application/pdf" width="100%" height="100%">
        <p>الملف..  <a href="{{ $url }}">بصيغة PDF!</a></p>
    </object>

</div>