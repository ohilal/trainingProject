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
                    <form class="user" method="POST" action="{{ route('event.update' , $event->id) }}">                    
                     @method('patch')
                @else
                    <form class="user" method="POST" action="{{ route('event.store') }}">
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
                <label for="main_image" class="col-form-label">  الصورة الرئيسية</label>
                        <input name="main_image" type="file" class="form-control-file border col-lg-4" id="main_image"
                            placeholder="الصورة الرئيسية" value="{{ $event->image ?? '' }}">
                        @error('main_image')
                            <span class="invalid-feedback" role="alert">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>
                     <div class="form-group row col-12"> 
                         <label for="eventType" class="col-form-label">   نوع الحدث</label> 
                         <select  class="form-control" name="eventType" id="eventType" value={{$event_types->eventName ?? ''}}>
                           </select>
                        </div>
                    <!-- SAVE BUTTON -->
                    <div class="form-group">
                        <input type="submit" class="btn btn-primary btn-user btn-block"
                            value="{{ __('حفظ') }}">
                    </div>
                </form>


            </div>
        </div>
    </div>
</div>


@endsection