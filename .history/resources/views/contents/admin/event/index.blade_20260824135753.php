
@extends('layouts.admin')


@section("content")
            <!-- Card Header - Dropdown -->

            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-uppercase">{{ __('Departments') }}</h6>

                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink" style="">
                        <div class="dropdown-header">{{ __('Action') }}</div>
                        @can('department.create')
                        <x-CreateButton path="{{ route('event.create') }}" />
                        @endcan
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{{ route('course.index') }}">
                            <i class="fas fa-arrow-right pr-2"></i>
                            {{ __("Courses") }}
                        </a>
                        <a class="dropdown-item" href="{{ route('term.index') }}">
                            <i class="fas fa-arrow-right pr-2"></i>
                            {{ __("Term") }}
                        </a>

                    </div>
                </div>
            </div>
<table class="table table-reponsive table-striped">
    <thead>
      <tr>
        <th>العنوان</th>
        <th>تاريخ الحدث</th>
        <th>الصورة الرئيسية</th>
      </tr>
    </thead>
 <tbody> 
    @foreach ($events as $event)
      <tr>
        <td> {{ $event['title'] }} </td>
        <td>{{ $event['start_date'] }} </td>
        <td>
      <img src="{{ asset('storage/files/events/' . $event->image) }}" style="max-width: 50px; ">
      </td>
      </tr>           
            @endforeach
      </tbody>
</table>
  <!-- @foreach ($events as $event)
            
<div class="col">
  <div class="card ">
    <img class="card-img-top img-responsive" src="/course/file.png" alt="Card image" >

    <div class="card-body ">
            <div class="card-title h3" data-filter="{{ $event['id'] }}">{{ $event['title'] }} </div>
           <i class="fa fa-calendar-alt text-muted"> </i> <small class="text-muted"> {{ $event['start_date'] }}   </small>

      <p class="card-text">
    

       
      </p>
    </div>
    
  </div>
</div>
 @endforeach           
      -->
@endsection