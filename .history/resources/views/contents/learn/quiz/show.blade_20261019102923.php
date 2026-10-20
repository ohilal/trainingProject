@extends('layouts.admin')


@section("content")
<div class="row">
    
    <div class="col-12">
        <div class="card shadow mb-4 border-bottom-primary">
            <!-- Card Header - Dropdown -->
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between" dir="{{ App::getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
                <h6 class="m-0 font-weight-bold text-primary {{ App::getLocale() === 'ar' ? 'text-right' : 'text-left' }}" >{{ $activity->title }}</h6>

                <div class="dropdown no-arrow">
                    <x-BackButton />
                </div>
            </div>
            <!-- Card Body -->
            <div class="card-body {{ App::getLocale() === 'ar' ? 'text-right' : 'text-left' }}">

                @livewire('activity.quiz', [
                'activity' => $activity,
                'participant' => $participant,
                'workout' => $workout
                ])
            </div>


            <div class="card-footer text-center">
                <button class="btn btn-primary" id="saveButton">
                    <i class="fa fa-save"></i> {{ __("حفظ") }}
                </button>
                <button class="btn btn-danger" id="saveAndClose" data-next-url="{{ route('completedAndNext', ['workout' => $workout->id]) }}">
                    <i class="fa fa-save"></i> {{ __("حفظ وإغلاق") }}
                </button>
            </div>
        </div>
    </div>

    <div class="d-none">
        <div id="dialog-confirm" title="Save And Close?">
            <p><span class="ui-icon ui-icon-alert" style="float:right; margin:12px 12px 20px 0;"></span>
                سيتم إغلاق هذه العناصر نهائياً ولن يمكن استعادتها. هل أنت متأكد؟</p>
        </div>
    </div>
    @endsection

    @section('js')
    <script>
        let mp3WorkerPathPHP = "{{ URL::to('js/record/src/mp3Worker.js') }}";
        let quizSavedMessage = @json(__('تم حفظ الإجابات بنجاح.'));
        let quizSaveErrorMessage = @json(__('تعذر حفظ الإجابات. يرجى المحاولة مرة أخرى.'));
    </script>
    <script src="{{ URL::to('/js/quiz.js') }}"></script>
    <script src="{{ URL::to('/js/record/src/recorder.js') }}"></script>
    <script src="{{ URL::to('/js/record/src/mp3Worker.js') }}"></script>
    <script src="{{ URL::to('/js/record/js/app.js') }}"></script>
    @endsection