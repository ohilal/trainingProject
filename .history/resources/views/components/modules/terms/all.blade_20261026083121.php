<div>

    @foreach($terms as $term)

    <div class="row">
        <div class="col-12 mb-2">
            <div class="card border-left-primary bg-light shadow">
                <div class="card-body p-4 text-left">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-4">
                            <div class="row">

                                @if(!$terms->is_mentor)

                                <div class="col-2">
                                    <img src="{{ URL::to('term/' . $term->Term->image) }}" class="img-fluid" alt="">
                                </div>

                                @endif


                                <div class="col ml-3 text-right">
                                    <p style="background: var();"
                                        {{ $term->Term->Department->title }} 
                                       <h6 class="text-secondary ">  
                                       <i class="fas fa-angle-double-left"></i>
                                       <span class="fs-4 text-dark"> {{ $term->Term->Course->title }}</span>
                                       
                                    </h6>

                                    <div class="text-lg pt-2 font-weight-bold mb-1 text-primary text-right">
                                        <a class="text-secondary" href="{{ route($terms->is_mentor ? 'learnerParticipantWorkout' : 'learningCourse', $term->id) }}">
                                            {{ $term->Term->title }}
                                        </a>
                                    </div>

                                    {{ $slot }}
                                    
                                    <x-atoms.progress :color="'progress-bar-striped bg-success'" :fill="$term->Term->task_done ?? 0" :count="$term->Term->total_task ?? 0">
                                    </x-atoms.progress>

                                </div>

                                @if(!$terms->is_mentor)
                                <div class="col-4">
                                    <h6 class="font-weight-bold text-dark">{{ __(' التالى') }}</h6>
                                    @if($term->next_activity)
                                    <p class="text-dark">
                                        <i class="fa fa-flag-checkered"></i>
                                        <a href="{{ $term->next_activity['url'] }}" class="">{{ $term->next_activity['title'] }}</a>
                                    </p>
                                    <small class="text-secondary">
                                        {{ $term->next_activity['session_title'] }}
                                        @if($term->next_activity['estimated_minutes'] > 0)
                                            | {{ $term->next_activity['estimated_minutes'] }} min
                                        @endif
                                    </small>
                                    @else
                                    <p class="text-secondary">{{ __('All activities completed') }}</p>
                                    @endif
                                </div>
                                @endif
                            </div>

                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>

    @endforeach
</div>