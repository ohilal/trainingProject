    <!-- Earnings (Monthly) Card Example -->
    <!-- <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Earnings (Monthly)</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">$40,000</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div> -->

    <!-- Earnings (Monthly) Card Example -->
    <!-- <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Earnings (Annual)</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">$215,000</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div> -->

    <!-- Course progress -->
    <div class="col-lg-3 col-md-6 col-sm-12">
        <div class="card border-bottom-info shadow mb-4" style="height: 100%;">
            <div class="card-header  py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold "><i class="fas fa-tasks"></i> المهام</h6>
            </div>
            <div class="card-body p-2" style="max-height: 260px; overflow-y: auto; direction: rtl;">
                @forelse($courseProgress as $course)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <div class="text-xs font-weight-bold text-info text-truncate mr-2" title="{{ $course['title'] }}">
                                {{ $course['title'] }}
                            </div>
                            <div class="badge rounded-pill bg-info">{{ $course['percentage'] }}%</div>
                        </div>
                        <div class="progress progress-sm mb-1">
                            <div
                                class="progress-bar bg-info"
                                role="progressbar"
                                style="width: {{ $course['percentage'] }}%"
                                aria-valuenow="{{ $course['percentage'] }}"
                                aria-valuemin="0"
                                aria-valuemax="100"
                            ></div>
                        </div>
                        <small class="text-secondary">{{ __('الإنجاز') }}
                            {{ $course['completed_tasks'] }}/{{ $course['total_tasks'] }} 
                        </small>
                    </div>
                @empty
                    <div class="text-center text-secondary py-3">{{ __('لا توجد دورات تدريبية') }}</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Pending Requests Card Example -->
@can('mentor.list')
    <div class="col-lg-2 col-md-6 col-sm-12">
        <div class="card border-bottom-warning shadow mb-4" style="height: 100%;">
            <div class="card-header  py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold "><i class="fas fa-comment-dots"></i> الطلبات المعلقة  </h6>
            </div>
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Pending Requests</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"></div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-comments fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endcan