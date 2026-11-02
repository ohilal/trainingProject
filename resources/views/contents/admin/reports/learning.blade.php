@extends('layouts.admin')

@section('content')
@php
    $attendanceLabels = [
        'completed'   => ['badge' => 'success', 'text' => __('Completed')],
        'attending'   => ['badge' => 'info',    'text' => __('Attending')],
        'not_started' => ['badge' => 'secondary', 'text' => __('Not started')],
    ];

    // Chart data prepared in PHP (avoids multi-line @json([...]) Blade parsing issues).
    $reportData = [
        'mostViewed'    => collect($report['most_viewed_courses'])->map(fn ($c) => [
            'title'    => $c['course_title'],
            'starts'   => $c['activity_starts'],
            'learners' => $c['active_learner_count'],
        ])->values()->all(),
        'mostCompleted' => collect($report['most_completed_courses'])->map(fn ($c) => [
            'title'    => $c['course_title'],
            'rate'     => $c['completion_rate'],
            'done'     => $c['completed_enrollments'],
            'learners' => $c['learner_count'],
        ])->values()->all(),
        'attendance'    => [
            ['label' => __('Not started'), 'value' => collect($report['rows'])->where('attendance_status', 'not_started')->count(), 'color' => '#d1d3e2'],
            ['label' => __('Attending'),   'value' => collect($report['rows'])->where('attendance_status', 'attending')->count(),   'color' => '#36b9cc'],
            ['label' => __('Completed'),   'value' => collect($report['rows'])->where('attendance_status', 'completed')->count(),   'color' => '#1cc88a'],
        ],
        'departments'   => collect($report['department_rankings'])->take(10)->map(fn ($d) => [
            'name'     => $d['name'],
            'learners' => $d['learner_count'],
            'learning' => $d['learning_score'],
            'quiz'     => $d['quiz_score'],
        ])->values()->all(),
        'topLearners'   => collect($report['rows'])
            ->groupBy('user_id')
            ->map(fn ($r) => [
                'name'  => $r->first()['learner_name'],
                'ou'    => $r->first()['ldap_ou'],
                'score' => round((float) $r->avg('learning_score'), 1),
            ])
            ->sortByDesc('score')->take(10)->values()->all(),
    ];
@endphp

<style>
    /* ---- Learning report visual enhancements (sb-admin-2 theme) ---- */
    .lr-page-head {
        background: linear-gradient(135deg, #4e73df 0%, #2b56c4 60%, #224abe 100%);
        border-radius: .5rem;
    }
    .lr-icon-badge { width: 42px; height: 42px; box-shadow: 0 .25rem .4rem rgba(0,0,0,.18); }
    .lr-stat-card { transition: transform .15s ease, box-shadow .15s ease; }
    .lr-stat-card:hover { transform: translateY(-3px); box-shadow: 0 .5rem 1.2rem rgba(58,59,69,.25) !important; }
    .lr-icon-watermark { font-size: 3.2rem; opacity: .18; flex-shrink: 0; }
    .card-header.lr-chart-header { position: relative; overflow: hidden; }
    .card-header.lr-chart-header::after {
        content: ""; position: absolute; top: 0; left: 0; right: 0; height: 3px;
        background: linear-gradient(90deg, #4e73df, #36b9cc, #1cc88a);
    }
    .table thead th { background-color: #f8f9fc; white-space: nowrap; }
    .table-hover tbody tr:hover { background-color: rgba(78,115,223,.05); }
    .lr-rank {
        display: inline-flex; align-items: center; justify-content: center;
        width: 26px; height: 26px; border-radius: 50%; font-size: .72rem; font-weight: 700; color: #fff;
    }
    .lr-rank-1 { background: linear-gradient(180deg, #f6c23e, #dda20a); }
    .lr-rank-2 { background: linear-gradient(180deg, #b6c2d9, #858796); }
    .lr-rank-3 { background: linear-gradient(180deg, #d99a6c, #b87333); }
    .lr-rank-n { background: #dde3ec; color: #5a5c69; }
    .lr-medal { color: #dda20a; }
</style>

<div class="row mb-4">
    <div class="col-12 lr-page-head text-white shadow py-4 px-4 d-flex align-items-center justify-content-between">
        <div>
            <h1 class="h3 mb-1 text-white"><i class="fas fa-chart-pie mr-2"></i>{{ __('Learning report') }}</h1>
            <p class="mb-0" style="opacity:.85;">{{ __('Course attendance, learner progress, activity starts, completions, and scores by LDAP OU.') }}</p>
        </div>
        <i class="fas fa-graduation-cap" style="font-size:3rem; opacity:.25;"></i>
    </div>
</div>

<div class="alert alert-info d-flex align-items-center">
    <i class="fas fa-info-circle fa-lg mr-2"></i>
    <span>{{ __('LDAP OU values are refreshed when learners sign in. Existing LDAP learners should sign in again to refresh this value.') }}</span>
</div>

{{-- ============================ Filters ============================ --}}
<div class="card shadow mb-4">
    <div class="card-header lr-chart-header py-3 bg-white">
        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-filter mr-1"></i> {{ __('Filters') }}</h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.reports.learning') }}" class="form-row align-items-end">
            <div class="form-group col-md-4">
                <label for="attendance">{{ __('Attendance') }}</label>
                <select name="attendance" id="attendance" class="form-control">
                    <option value="all" {{ $report['attendance_filter'] === 'all' ? 'selected' : '' }}>{{ __('All attendance') }}</option>
                    <option value="not_started" {{ $report['attendance_filter'] === 'not_started' ? 'selected' : '' }}>{{ __('Not started') }}</option>
                    <option value="attending" {{ $report['attendance_filter'] === 'attending' ? 'selected' : '' }}>{{ __('Attending') }}</option>
                    <option value="completed" {{ $report['attendance_filter'] === 'completed' ? 'selected' : '' }}>{{ __('Completed') }}</option>
                </select>
            </div>
            <div class="form-group col-md-5">
                <label for="activity">{{ __('Activity') }}</label>
                <select name="activity" id="activity" class="form-control">
                    <option value="">{{ __('All activities') }}</option>
                    @foreach($report['activities'] as $activity)
                    <option value="{{ $activity['id'] }}" {{ $report['selected_activity'] === $activity['id'] ? 'selected' : '' }}>
                        {{ $activity['label'] }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group col-md-3">
                <button type="submit" class="btn btn-primary"><i class="fas fa-check mr-1"></i>{{ __('Apply filters') }}</button>
                <a href="{{ route('admin.reports.learning') }}" class="btn btn-light"><i class="fas fa-undo mr-1"></i>{{ __('Reset') }}</a>
            </div>
        </form>
    </div>
</div>

{{-- ============================ Course overview ============================ --}}
<div class="card shadow mb-4">
    <div class="card-header lr-chart-header py-3 bg-white d-flex justify-content-between align-items-center">
        <div>
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-book-open mr-1"></i> {{ __('Course overview') }}</h6>
            <small class="text-muted">{{ __('All courses are listed, including courses with no learner activity.') }}</small>
        </div>
        <button class="btn btn-sm btn-outline-primary collapsed" data-toggle="collapse" data-target="#chartOverviewWrap" aria-expanded="false">
            <i class="fas fa-chart-bar fa-sm"></i> {{ __('Chart') }}
        </button>
    </div>
    {{-- Chart.js: most viewed / most completed side by side --}}
    <div id="chartOverviewWrap" class="collapse">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-7 mb-3 mb-lg-0">
                    <h6 class="text-gray-800 font-weight-bold">{{ __('Most viewed courses') }} <small class="text-muted">({{ __('activity starts') }})</small></h6>
                    <div style="position:relative; height:300px;"><canvas id="chartMostViewed"></canvas></div>
                </div>
                <div class="col-lg-5">
                    <h6 class="text-gray-800 font-weight-bold">{{ __('Most completed courses') }} <small class="text-muted">({{ __('completion rate %') }})</small></h6>
                    <div style="position:relative; height:300px;"><canvas id="chartMostCompleted"></canvas></div>
                </div>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-sm mb-0">
            <thead>
                <tr>
                    <th>{{ __('الدورة التدريبية') }}</th>
                    <th>{{ __('البرنامج التدريبى') }}</th>
                    <th>{{ __('المتدربون') }}</th>
                    <th>{{ __('المتدربون الذين بدأوا') }}</th>
                    <th>{{ __('متوسط التقدم') }}</th>
                    <th>{{ __('بدء النشاط') }}</th>
                    <th>{{ __('الدورات المكتملة') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($report['courses'] as $course)
                <tr>
                    <td>{{ $course['course_title'] }}</td>
                    <td>{{ $course['department'] ?? '—' }}</td>
                    <td>{{ $course['learner_count'] }}</td>
                    <td>{{ $course['active_learner_count'] }}</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="progress flex-grow-1 mr-2" style="height:8px; min-width:60px;">
                                <div class="progress-bar bg-info" style="width: {{ $course['average_progress'] }}%"></div>
                            </div>
                            <small>{{ $course['average_progress'] }}%</small>
                        </div>
                    </td>
                    <td>{{ $course['activity_starts'] }}</td>
                    <td>{{ $course['completed_enrollments'] }}</td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center text-muted">{{ __('No courses found.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ============================ Summary cards ============================ --}}
@php
    $summaryCards = [
        ['value' => $report['summary']['learners'],              'label' => __('Learners'),             'icon' => 'fa-users',      'gradient' => 'bg-gradient-primary', 'icon_bg' => 'bg-primary'],
        ['value' => $report['summary']['attending_learners'],    'label' => __('Attending learners'),   'icon' => 'fa-user-graduate','gradient' => 'bg-gradient-info',  'icon_bg' => 'bg-info'],
        ['value' => $report['summary']['average_progress'] . '%','label' => __('Average progress'),     'icon' => 'fa-chart-line', 'gradient' => 'bg-gradient-success', 'icon_bg' => 'bg-success'],
        ['value' => $report['summary']['completed_enrollments'], 'label' => __('Completed enrollments'),'icon' => 'fa-clipboard-check','gradient' => 'bg-gradient-warning','icon_bg' => 'bg-warning'],
    ];
@endphp
<div class="row">
    @foreach($summaryCards as $card)
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card {{ $card['gradient'] }} text-white shadow h-100 border-0 overflow-hidden lr-stat-card">
            <div class="card-body d-flex align-items-center justify-content-between position-relative">
                <div class="lr-stat-text">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle lr-icon-badge {{ $card['icon_bg'] }} mb-2">
                        <i class="fas {{ $card['icon'] }} fa-sm text-white"></i>
                    </div>
                    <div class="text-xs font-weight-bold text-uppercase mb-1" style="opacity:.9;">{{ $card['label'] }}</div>
                    <div class="h4 mb-0 font-weight-bold text-white">{{ $card['value'] }}</div>
                </div>
                <i class="fas {{ $card['icon'] }} lr-icon-watermark text-white"></i>
            </div>
        </div>
    </div>
    @endforeach
</div>

{{-- ============================ Attendance donut + rankings ============================ --}}
<div class="row">
    <div class="col-lg-4 mb-4">
        <div class="card shadow h-100">
            <div class="card-header lr-chart-header py-3 bg-white">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-user-check mr-1"></i> {{ __('Attendance status') }}</h6>
                <small class="text-muted">{{ __('Learner/course-term rows matching the filters.') }}</small>
            </div>
            <div class="card-body">
                <div style="position:relative; height:260px;"><canvas id="chartAttendance"></canvas></div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 mb-4">
        <div class="card shadow h-100">
            <div class="card-header lr-chart-header py-3 bg-white">
                <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-eye mr-1"></i> {{ __('Most viewed courses') }}</h6>
                <small class="text-muted">{{ __('Ranked by learners who started an activity; activity starts are shown separately.') }}</small>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead><tr><th>{{ __('الدورة التدريبية') }}</th><th>{{ __('المتدربون') }}</th><th>{{ __('بدء النشاط') }}</th></tr></thead>
                    <tbody>
                        @forelse($report['most_viewed_courses'] as $course)
                        <tr>
                            <td>
                                <span class="lr-rank {{ $loop->index < 3 ? 'lr-rank-' . ($loop->index + 1) : 'lr-rank-n' }} mr-1">{{ $loop->iteration }}</span>
                                {{ $course['course_title'] }}
                            </td>
                            <td>{{ $course['active_learner_count'] }}</td>
                            <td><span class="badge badge-light border"><i class="fas fa-play fa-xs text-primary mr-1"></i>{{ $course['activity_starts'] }}</span></td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center text-muted py-3"><i class="fas fa-inbox d-block mb-1"></i>{{ __('No activity starts match these filters.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4 mb-4">
        <div class="card shadow h-100">
            <div class="card-header lr-chart-header py-3 bg-white">
                <h6 class="m-0 font-weight-bold text-success"><i class="fas fa-trophy mr-1"></i> {{ __('Most completed courses') }}</h6>
                <small class="text-muted">{{ __('Ranked by learners who completed every activity in their course term.') }}</small>
            </div>
            <div class="table-responsive">
                <table class="table table-sm table-hover mb-0">
                    <thead><tr><th>{{ __('الدورة التدريبية') }}</th><th>{{ __('الدورات المكتملة') }}</th><th>{{ __('معدل الإكمال') }}</th></tr></thead>
                    <tbody>
                        @forelse($report['most_completed_courses'] as $course)
                        <tr>
                            <td>
                                @if($loop->first)<i class="fas fa-medal lr-medal mr-1"></i>@else<span class="lr-rank {{ $loop->index < 3 ? 'lr-rank-' . ($loop->index + 1) : 'lr-rank-n' }} mr-1">{{ $loop->iteration }}</span>@endif
                                {{ $course['course_title'] }}
                            </td>
                            <td>{{ $course['completed_enrollments'] }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="progress flex-grow-1 mr-2" style="height:8px; min-width:50px;">
                                        <div class="progress-bar bg-gradient-success" style="width: {{ $course['completion_rate'] }}%"></div>
                                    </div>
                                    <small>{{ $course['completion_rate'] }}%</small>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="3" class="text-center text-muted py-3"><i class="fas fa-inbox d-block mb-1"></i>{{ __('No completed courses match these filters.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- ============================ Department score rankings ============================ --}}
<div class="card shadow mb-4">
    <div class="card-header lr-chart-header py-3 bg-white d-flex justify-content-between align-items-center">
        <div>
            <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-sitemap mr-1"></i> {{ __('Department score rankings') }}</h6>
            <small class="text-muted">{{ __('Average scores are calculated per learner from completed learning activities and quizzes.') }}</small>
        </div>
        <button class="btn btn-sm btn-outline-primary collapsed" data-toggle="collapse" data-target="#chartDeptWrap" aria-expanded="false">
            <i class="fas fa-chart-bar fa-sm"></i> {{ __('Chart') }}
        </button>
    </div>
    <div id="chartDeptWrap" class="collapse">
        <div class="card-body">
            <div style="position:relative; height:320px;"><canvas id="chartDepartments"></canvas></div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered mb-0">
            <thead>
                <tr>
                    <th>{{ __('LDAP OU') }}</th>
                    <th>{{ __('المتدربون') }}</th>
                    <th>{{ __('متوسط التعلم') }}</th>
                    <th>{{ __('أفضل متدرب في التعلم') }}</th>
                    <th>{{ __('متوسط الاختبارات') }}</th>
                    <th>{{ __('أفضل متدرب في الاختبارات') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($report['department_rankings'] as $department)
                <tr>
                    <td>{{ $department['name'] }}</td>
                    <td>{{ $department['learner_count'] }}</td>
                    <td>{{ $department['learning_score'] === null ? '—' : $department['learning_score'] . '%' }}</td>
                    <td>
                        @if($department['top_learning_learner'])
                            {{ $department['top_learning_learner']['name'] }} ({{ $department['top_learning_learner']['score'] }}%)
                        @else
                            —
                        @endif
                    </td>
                    <td>{{ $department['quiz_score'] === null ? '—' : $department['quiz_score'] . '%' }}</td>
                    <td>
                        @if($department['top_quiz_learner'])
                            {{ $department['top_quiz_learner']['name'] }} ({{ $department['top_quiz_learner']['score'] }}%)
                        @else
                            —
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center text-muted">{{ __('No learner score data matches these filters.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ============================ Learner course details ============================ --}}
<div class="card shadow mb-4">
    <div class="card-header lr-chart-header py-3 bg-white d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-user-clock mr-1"></i> {{ __('Learner course details') }}</h6>
        <button class="btn btn-sm btn-outline-primary collapsed" data-toggle="collapse" data-target="#chartLearnersWrap" aria-expanded="false">
            <i class="fas fa-chart-bar fa-sm"></i> {{ __('Top 10 learners chart') }}
        </button>
    </div>
    <div id="chartLearnersWrap" class="collapse">
        <div class="card-body">
            <div style="position:relative; height:360px;"><canvas id="chartTopLearners"></canvas></div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-sm mb-0">
            <thead>
                <tr>
                    <th>{{ __('المتدرب') }}</th>
                    <th>{{ __('LDAP OU') }}</th>
                    <th>{{ __('الدورة التدريبية') }}</th>
                    <th>{{ __('قسم الدورة') }}</th>
                    <th>{{ __('الحضور') }}</th>
                    @if($report['selected_activity'] !== null)<th>{{ __('النشاط المحدد') }}</th>@endif
                    <th>{{ __('الإنجاز') }}</th>
                    <th>{{ __('Activity starts') }}</th>
                    <th>{{ __('Learning score') }}</th>
                    <th>{{ __('Quiz score') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($report['rows'] as $row)
                @php $st = $attendanceLabels[$row['attendance_status']] ?? ['badge' => 'secondary', 'text' => $row['attendance_status']]; @endphp
                <tr>
                    <td>{{ $row['learner_name'] }}</td>
                    <td>{{ $row['ldap_ou'] }}</td>
                    <td>{{ $row['course_title'] }} <small class="text-muted d-block">{{ $row['term_title'] }}</small></td>
                    <td>{{ $row['course_department'] ?? '—' }}</td>
                    <td><span class="badge badge-{{ $st['badge'] }}">{{ $st['text'] }}</span></td>
                    @if($report['selected_activity'] !== null)
                    <td>{{ __(ucwords(str_replace('_', ' ', $row['activity_status']))) }}</td>
                    @endif
                    <td>
                        <div class="progress">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $row['progress'] }}%" aria-valuenow="{{ $row['progress'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <small>{{ $row['completed_activities'] }}/{{ $row['total_activities'] }} ({{ $row['progress'] }}%)</small>
                    </td>
                    <td>{{ $row['activity_starts'] }}</td>
                    <td>{{ $row['learning_score'] === null ? '—' : $row['learning_score'] . '%' }}</td>
                    <td>{{ $row['quiz_score'] === null ? '—' : $row['quiz_score'] . '%' }}</td>
                </tr>
                @empty
                <tr><td colspan="{{ $report['selected_activity'] !== null ? 10 : 9 }}" class="text-center text-muted">{{ __('No learners match these filters.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('js')
<script src="{{ URL::to('vendor/chart.js/Chart.min.js') }}"></script>
<script>
(function () {
    'use strict';

    if (typeof Chart === 'undefined') {
        console.error('Chart.js failed to load from vendor/chart.js/Chart.min.js');
        return;
    }

    // ---------- Data injected from PHP ----------
    var data = @json($reportData);

    var built = {};
    function build(id) { if (charts[id] && !built[id]) { charts[id](); built[id] = true; } }
    function onShown(e) { build(e.target.id); }
    var charts = {};

    $('#chartOverviewWrap').on('shown.bs.collapse', function () { build('chartOverviewWrap'); });
    $('#chartDeptWrap').on('shown.bs.collapse', onShown);
    $('#chartLearnersWrap').on('shown.bs.collapse', onShown);

    var palette = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#6f42c1', '#fd7e14', '#20c9a6'];

    // ---------- 1. Overview panel: most viewed (bars) + most completed (doughnut), built lazily on expand ----------
    charts['chartOverviewWrap'] = function () {
    new Chart(document.getElementById('chartMostViewed').getContext('2d'), {
        type: 'horizontalBar',
        data: {
            labels: data.mostViewed.map(function (c) { return c.title; }),
            datasets: [{
                label: @json(__('Activity starts')),
                data: data.mostViewed.map(function (c) { return c.starts; }),
                backgroundColor: '#4e73df',
                borderRadius: 3,
            }]
        },
        options: {
            maintainAspectRatio: false,
            legend: { display: false },
            scales: { xAxes: [{ ticks: { beginAtZero: true, precision: 0 } }] },
            tooltips: {
                callbacks: {
                    afterLabel: function (tip) {
                        var c = data.mostViewed[tip.index];
                        return @json(__('Learners')) + ': ' + c.learners;
                    }
                }
            }
        }
    });

    new Chart(document.getElementById('chartMostCompleted').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: data.mostCompleted.map(function (c) { return c.title; }),
            datasets: [{
                data: data.mostCompleted.map(function (c) { return c.rate; }),
                backgroundColor: data.mostCompleted.map(function (_, i) { return palette[i % palette.length]; }),
            }]
        },
        options: {
            maintainAspectRatio: false,
            legend: { position: 'bottom', labels: { boxWidth: 12 } },
            tooltips: {
                callbacks: {
                    label: function (tip) {
                        var c = data.mostCompleted[tip.index];
                        return c.title + ': ' + c.done + '/' + c.learners + ' (' + c.rate + '%)';
                    }
                }
            }
        }
    });
    }; /* end lazy overview charts */

    // ---------- 3. Attendance status (donut) ----------
    new Chart(document.getElementById('chartAttendance').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: data.attendance.map(function (a) { return a.label; }),
            datasets: [{
                data: data.attendance.map(function (a) { return a.value; }),
                backgroundColor: data.attendance.map(function (a) { return a.color; }),
            }]
        },
        options: {
            maintainAspectRatio: false,
            cutoutPercentage: 60,
            legend: { position: 'bottom', labels: { boxWidth: 12 } }
        }
    });

    // ---------- 4. Departments: learners (bars) + avg scores (lines) ----------
    charts['chartDeptWrap'] = function () {
    new Chart(document.getElementById('chartDepartments').getContext('2d'), {
        type: 'bar',
        data: {
            labels: data.departments.map(function (d) { return d.name; }),
            datasets: [
                {
                    label: @json(__('Learners')),
                    type: 'bar',
                    data: data.departments.map(function (d) { return d.learners; }),
                    backgroundColor: 'rgba(78,115,223,.6)',
                    yAxisID: 'yLearners',
                },
                {
                    label: @json(__('Average learning score (%)')),
                    type: 'line',
                    data: data.departments.map(function (d) { return d.learning; }),
                    borderColor: '#1cc88a',
                    backgroundColor: '#1cc88a',
                    fill: false,
                    yAxisID: 'yScore',
                },
                {
                    label: @json(__('Average quiz score (%)')),
                    type: 'line',
                    data: data.departments.map(function (d) { return d.quiz; }),
                    borderColor: '#f6c23e',
                    backgroundColor: '#f6c23e',
                    fill: false,
                    yAxisID: 'yScore',
                }
            ]
        },
        options: {
            maintainAspectRatio: false,
            scales: {
                yLearners: { position: 'left', ticks: { beginAtZero: true, precision: 0 } },
                yScore: { position: 'right', ticks: { beginAtZero: true, max: 100 }, gridLines: { drawOnChartArea: false } }
            }
        }
    });

    }; /* end dept chart builder */

    // ---------- 5. Top learners (horizontal bars) ----------
    charts['chartLearnersWrap'] = function () {
    new Chart(document.getElementById('chartTopLearners').getContext('2d'), {
        type: 'horizontalBar',
        data: {
            labels: data.topLearners.map(function (l) { return l.name; }),
            datasets: [{
                label: @json(__('Learning score (%)')),
                data: data.topLearners.map(function (l) { return l.score; }),
                backgroundColor: '#1cc88a',
            }]
        },
        options: {
            maintainAspectRatio: false,
            legend: { display: false },
            scales: { xAxes: [{ ticks: { beginAtZero: true, max: 100 } }] },
            tooltips: {
                callbacks: {
                    afterLabel: function (tip) {
                        return data.topLearners[tip.index].ou;
                    }
                }
            }
        }
    });
    }; /* end top-learners builder */

    // Build charts that live in initially-open cards immediately;
    // charts inside collapsed panels are built lazily on first expand.
    build('chartOverviewWrap');

    // Redraw visible charts when a collapse animation finishes or the window resizes.
    $('.collapse').on('shown.bs.collapse', function () {
        Object.keys(built).forEach(function (id) {
            var canvas = document.querySelector('#' + id + ' canvas');
            if (!canvas) return;
            var inst = Chart.getChart ? Chart.getChart(canvas) : Chart.instances[canvas.id];
            if (inst) inst.resize();
        });
    });
    window.addEventListener('resize', function () {
        Object.values(Chart.instances || {}).forEach(function (c) { try { c.resize(); } catch (e) {} });
    });
})();
</script>
@endpush
