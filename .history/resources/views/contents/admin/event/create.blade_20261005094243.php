@extends('layouts.admin')


@section("content")

<!-- Create Form Card -->
<div class="col-12">
    <div class="card shadow mb-4 border-bottom-primary">
        <!-- Card Header - Dropdown -->
        <div
            class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">{{ __("Events/تكريم") }}</h6>
            <div class="dropdown no-arrow">
                <x-BackButton />
            </div>
        </div>
        <!-- Card Body -->
        <div class="card-body">
            <div class="text-center">

                @if(isset($event))
                    <form class="user" method="POST" enctype="multipart/form-data" action="{{ route('event.update' , $event->id) }}">                    
                     @method('patch')
                @else
                    <form class="user" method="POST" enctype="multipart/form-data" action="{{ route('event.store') }}">
                @endif
                
                    @csrf
                    <div class="form-group row">
                        <div class="col-sm-6 mb-3 mb-sm-0">
                            <input name="title" type="text" class="form-control form-control-user" id="title"
                                placeholder="العنوان" value="{{ $event->title ?? '' }}">
                            @error('title')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror    
                        </div>
                    </div>
                    <div class="form-group">
                        <textarea name="description" type="text" class="form-control editor" id="description"
                            placeholder="الوصف">{{ $event->description ?? '' }}</textarea>
                        @error('description')
                            <span class="invalid-feedback" role="alert">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
        <div class="form-group row col-12">
            <label for="" class="col-form-label">تاريخ الحدث</label>
                        <input name="start_date" type="date" class="form-control col-lg-4 ml-5" id="start_date"
                            placeholder="تاريخ البداية" value="{{ $event->start_date ?? '' }}">
                        @error('start_date')
                            <span class="invalid-feedback" role="alert">
                                {{ $message }}
                            </span>
                        @enderror
                 <label for="" class="col-form-label">تاريخ انتهاء الحدث</label>
                        <input name="end_date" type="date" class="form-control col-lg-4" id="end_date"
                            placeholder="تاريخ النهاية" value="{{ $event->end_date ?? '' }}">
                        @error('end_date')
                            <span class="invalid-feedback" role="alert">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
                    <div class="form-group row col-12"> 
    <label for="image" class="col-form-label">الصورة الرئيسية</label>
    
    {{-- Display Current Image if Editing --}}
    @if(isset($event) && $event->image)
        <div class="mb-2">
            <img src="{{ asset('storage/files/events/' . $event->image) }}" 
                 alt="{{ $event->title }}" 
                 style="max-width: 150px; max-height: 150px; object-fit: cover; border: 1px solid #ddd; padding: 5px;">
            <p class="text-muted small mt-1">اختر ملفاً جديداً لاستبدال الصورة الحالية.</p>
        </div>
    @endif

    {{-- File Input --}}
    <input name="image" type="file" class="form-control-file border col-lg-4" id="image"
        placeholder="الصورة الرئيسية">
    
    @error('image')
        <span class="invalid-feedback d-block" role="alert">
            {{ $message }}
        </span>
    @enderror
</div>
                   <!-- <div class="form-group row col-12"> 
                <label for="main_image" class="col-form-label">  الصورة الرئيسية</label>
                        <input name="image" type="file" class="form-control-file border col-lg-4" id="image"
                            placeholder="الصورة الرئيسية" value="{{ $event->image ?? '' }}">
                        @error('main_image')
                            <span class="invalid-feedback" role="alert">
                                {{ $message }}
                            </span>
                        @enderror
                    </div> -->
                     <div class="form-group row col-12"> 
                         <label for="eventType" class="col-form-label">   نوع الحدث</label> 
                           
    <select name="event_type_id" id="event_type_id" class="form-control col-lg-4" required name="event_type_id">
        <option value="">-- اختر نوع التكريم --</option>
          @foreach($eventTypes as $type)
        <option value="{{ $type->id }}" 
                        {{ old('event_type_id', $event->event_type_id ?? '') == $type->id ? 'selected' : '' }}>
            {{ $type->eventName }}
        </option>
    @endforeach


        <!-- @foreach($eventTypes as $type)
            <option value="{{ $type->id }}">
                {{ $type->eventName }} 
                {{-- Change $type->name to whatever column holds the display text, e.g., $type->title --}}
            </option>
        @endforeach -->
        
    </select>
    
    @error('event_type_id')
        <span class="text-danger">{{ $message }}</span>
    @enderror
   
    </div>
          {{-- ================= GALLERY (Lightbox2) ================= --}}
	                    <div class="form-group row col-12 mt-4">
                        <label for="gallery" class="col-form-label">معرض الصور</label>
	
	                        {{-- Existing gallery images (only when editing) --}}
	                        @if(isset($event) && $event->images->count())

   	                            <div class="row g-2 mb-3 event-gallery" id="existing-gallery">
	                                @foreach($event->images as $img)
	                                    <div class="col-6 col-md-3" id="gallery-item-{{ $img->id }}">
	                                        <a href="{{ asset('storage/files/events/' . $img->image) }}"
                                           data-lightbox="event-{{ $event->id }}"
	                                           data-title="{{ $img->caption ?? $event->title }}">
	                                            <img src="{{ asset('storage/files/events/' . $img->image) }}"
                                                 alt="{{ $img->caption ?? $event->title }}"
	                                                 class="img-fluid rounded border"
                                                 style="height: 110px; width: 100%; object-fit: cover;">
                                        </a>
                                        <button type="button"
	                                                class="btn btn-danger btn-sm mt-1 delete-gallery-image"
	                                                data-image-id="{{ $img->id }}">
	                                            <i class="fas fa-trash"></i> حذف
                                        </button>
	                                    </div>
	                                @endforeach
                            </div>
	                        @endif

	                        {{-- Upload new gallery images --}}
	                        <input name="gallery[]" type="file" id="gallery" class="form-control-file border"
	                               accept="image/*" multiple>
	                        <small class="text-muted">يمكن اختيار عدة صور دفعة واحدة (jpeg, png, jpg, gif, webp)</small>

	                        @error('gallery.*')
	                            <span class="invalid-feedback d-block" role="alert">{{ $message }}</span>
	                        @enderror
	                    </div>
                    <!-- SAVE BUTTON -->
                    <div class="form-group">
                        <input type="submit" class="btn btn-primary btn-user col-lg-2 btn-sm "
                            value="{{ __('حفظ') }}">
                    </div>
                </form>


            </div>
        </div>
    </div>
</div>

@endsection
@section('js')
67
   178	<script>
68
   179	    // Preview images selected in the "gallery[]" file input (before saving)
69
   180	    document.addEventListener('DOMContentLoaded', function () {
70
   181	        const input = document.getElementById('gallery');
71
   182	        if (!input) return;
72
   183	
73
   184	        input.addEventListener('change', function () {
74
   185	            let preview = document.getElementById('gallery-preview');
75
   186	            if (!preview) {
76
   187	                preview = document.createElement('div');
77
   188	                preview.id = 'gallery-preview';
78
   189	                preview.className = 'row g-2 mt-2';
79
   190	                input.parentNode.insertBefore(preview, input.nextSibling);
80
   191	            }
81
   192	            preview.innerHTML = '';
82
   193	
83
   194	            Array.from(this.files).forEach(file => {
84
   195	                const col  = document.createElement('div');
85
   196	                col.className = 'col-6 col-md-3';
86
   197	                const img  = document.createElement('img');
87
   198	                img.src = URL.createObjectURL(file);
88
   199	                img.className = 'img-fluid rounded border';
89
   200	                img.style = 'height:110px;width:100%;object-fit:cover;';
90
   201	                col.appendChild(img);
91
   202	                preview.appendChild(col);
92
   203	            });
93
   204	        });
94
   205	    });
95
   206	
96
   207	    // Delete an existing gallery image via AJAX (edit mode)
97
   208	    $(document).on('click', '.delete-gallery-image', function () {
98
   209	        const id = $(this).data('image-id');
99
   210	        const btn = $(this);
100
   211	        if (!confirm('هل أنت متأكد من حذف هذه الصورة؟')) return;
101
   212	
102
   213	        $.ajax({
103
   214	            url: '{{ url('event-image') }}/' + id,
104
   215	            method: 'POST',
105
   216	             { _method: 'DELETE', _token: '{{ csrf_token() }}' },
106
   217	            success: function () {
107
   218	                $('#gallery-item-' + id).remove();
108
   219	            },
109
   220	            error: function () {
110
   221	                alert('تعذر حذف الصورة');
111
   222	            }
112
   223	        });
	    });
	</script>
	@endsection