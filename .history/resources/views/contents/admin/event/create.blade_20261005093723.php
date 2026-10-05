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
7
   128	                    <div class="form-group row col-12 mt-4">
8
   129	                        <label for="gallery" class="col-form-label">معرض الصور</label>
9
   130	
10
   131	                        {{-- Existing gallery images (only when editing) --}}
11
   132	                        @if(isset($event) && $event->images->count())
12
   133	                            <div class="row g-2 mb-3 event-gallery" id="existing-gallery">
13
   134	                                @foreach($event->images as $img)
14
   135	                                    <div class="col-6 col-md-3" id="gallery-item-{{ $img->id }}">
15
   136	                                        <a href="{{ asset('storage/files/events/' . $img->image) }}"
16
   137	                                           data-lightbox="event-{{ $event->id }}"
17
   138	                                           data-title="{{ $img->caption ?? $event->title }}">
18
   139	                                            <img src="{{ asset('storage/files/events/' . $img->image) }}"
19
   140	                                                 alt="{{ $img->caption ?? $event->title }}"
20
   141	                                                 class="img-fluid rounded border"
21
   142	                                                 style="height: 110px; width: 100%; object-fit: cover;">
22
   143	                                        </a>
23
   144	                                        <button type="button"
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