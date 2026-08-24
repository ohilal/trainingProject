
@extends('layouts.admin')


@section("content")
            <!-- Card Header - Dropdown -->

            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-uppercase">{{ __('فعاليات') }}</h6>

                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink" style="">
                        <div class="dropdown-header">{{ __('Action') }}</div>
                        @can('event.create')
                        <x-CreateButton path="{{ route('event.create') }}" />
                        @endcan
                       

                    </div>
                </div>
            </div>
<table class="table table-reponsive table-striped">
    <thead>
      <tr>
        <th>العنوان</th>
        <th>تاريخ الحدث</th>
        <th>الصورة الرئيسية</th>
         @if(Auth::user()->hasRole('Super-Admin') || Auth::user()->hasAnyPermission(['event.edit' , 'event.delete']))
                                <th scope="col">{{ __("Action") }}</th>
                                @endif
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
       @if(Auth::user()->hasRole('Super-Admin') || Auth::user()->hasAnyPermission(['event.edit' , 'event.delete']))
                                <td>

                                    <div class="dropdown no-arrow">
                                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in" aria-labelledby="dropdownMenuLink" style="">
                                            <div class="dropdown-header">{{ __('Actions') }}:</div>
                                            @can('department.edit')
                                            <x-EditButton itemId="{{ $department->id }}" path="department.edit" />
                                            @endcan
                                            @can('department.delete')
                                            <x-DeleteButton itemId="{{ $department->id }}" path="department.destroy" />
                                            @endcan
                                        </div>
                                    </div>
                                </td>
                                @endif
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