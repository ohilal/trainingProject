@extends('layouts.admin')


@section("content")

<!-- Create Form Card -->
<div class="col-12">
    <div class="card shadow mb-4 border-bottom-primary">
        <!-- Card Header - Dropdown -->
        <div
            class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">{{ __("Course Form") }}</h6>
            <div class="dropdown no-arrow">
                <x-BackButton />
            </div>
        </div>
        <!-- Card Body -->
        <div class="card-body">
            <div class="text-center">

                @if(isset($course))
                    <form class="user" method="POST" action="{{ route('course.update' , $course->id) }}">                    
                     @method('patch')
                @else
                    <form class="user" method="POST" action="{{ route('course.store') }}">
                @endif
                
                    @csrf
                    <div class="form-group row">
                        <div class="col-sm-6 mb-3 mb-sm-0">
                            <input name="title" type="text" class="form-control form-control-user" id="title"
                                placeholder="Title" value="{{ $course->title ?? '' }}">
                            @error('title')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror    
                        </div>
                        <div class="col-sm-6">
                            <input name="image" type="text" class="form-control form-control-user" id="image"
                                placeholder="Image" value="{{ $course->image ?? '' }}">
                            @error('image')
                                <span class="invalid-feedback" role="alert">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-6 mb-3 mb-sm-0">
                    
                        </div>
                        
                        <div class="col-sm-6 mb-3 mb-sm-0">
                           <x-forms.DropDown model="Department" name="department_id" selected="{{ $course->department_id ?? 0 }}" />
                        </div>
                       
                    </div>
                    <div class="form-group">
                        <textarea name="description" type="text" class="form-control editor" id="description"
                            placeholder="Description">{{ $course->description ?? '' }}</textarea>
                        @error('description')
                            <span class="invalid-feedback" role="alert">
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    <div class="form-group text-right">
                        <input type="hidden" name="allow_self_enrollment" value="0">
                        <label>
                            <input type="checkbox" name="allow_self_enrollment" value="1"
                                {{ old('allow_self_enrollment', $course->allow_self_enrollment ?? false) ? 'checked' : '' }}>
                            {{ __('يسمح للمتدربين بالانضمام إلى هذه الدورة بأنفسهم') }}
                        </label>
                        <small class="form-text text-muted">
                            {{ __('إذا لم يتم تحديد هذا الخيار، فيجب على الأدمن تعيين المتدربين في الدورة التدريبية.') }}
                           <br/> {{ __('If unchecked, an administrator must assign learners to the course.') }}
                        </small>
                        @error('allow_self_enrollment')
                            <span class="invalid-feedback d-block" role="alert">{{ $message }}</span>
                        @enderror
                    </div>
   
                    
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