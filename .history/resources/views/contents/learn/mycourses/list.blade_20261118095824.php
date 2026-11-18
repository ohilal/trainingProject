@extends('layouts.admin')


@section("content")
<div class="row">

    <div class="col-12">
        <div class="card shadow mb-4 border-bottom-primary">
<!-- ----------------------- Profile--------------------------------->

        <div class="d-flex flex-column align-items-center text-center">
            <img src="{{ $user->avatar ? URL::to('img/profiles/' . $user->avatar) : URL::to('img/undraw_profile.svg') }}" alt="{{ $user->name }}" class="rounded" width="150">
            <div class="mt-3 ">
                <p class="text-secondary mb-1"><i class="fa fa-envelope"></i> {{ $user->email }}</p>
                <div class="px-2 rounded mt-4 date "><i class="fa fa-clock"></i> <span class="join">Joined {{ $user->created_at }}</span> </div>
            </div>
        </div>



<x-box.profile-review-box />
 <!------------------------- End Profile------------------------------>
            <!-- Card Header - Dropdown -->
            <div
                class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">{{ __("دوراتى التدريبية") }}</h6>
                <div class="dropdown no-arrow">
                    
                </div>
            </div>
            <!-- Card Body -->
            <div class="card-body">
                <div class="text-center">
                    
                    <ul class="nav nav-tabs">
                        <li class="nav-item active">
                            <a class="nav-link active" data-toggle="tab" href="#home"><i class="fas fa-house-user"></i></a>
                        </li>
                      </ul>
                      
                    <div class="tab-content p-4">
                        
                        <div id="home" class="tab-pane active">
                            <x-modules.terms.all :terms="$terms" />
                       
                        </div>
                    </div>
                
        

                </div>
            </div>
        </div>
    </div>


@endsection