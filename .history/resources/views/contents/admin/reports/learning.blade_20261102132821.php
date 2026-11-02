@extends('layouts.admin')

@section('content')
@php
    $attendanceLabels = [
        'completed'   => ['badge' => 'success', 'text' => __('Completed')],
        'attending'   => ['badge' => 'info',    'text' => __('Attending')],
        'not_started' => ['badge' => 'secondary', 'text' => __('Not started')],
    ];
    $formatLdapOu = static function ($value) {
        $sections = array_map('trim', explode('/', (string) $value));

        return implode(' / ', array_slice($sections, -2));
    };
@endphp
<style>::after</style
<div class="row mb-4">
    <div class="col-12">
        <h1 class="h3 mb-1 text-gray-800">{{ __('تقرير إحصائى') }}</h1>
        <p class="text-muted mb-0">{{ __('حضور الدورات التدريبية، وتقدم المتعلمين، وبدء الأنشطة، وإتمامها، والدرجات، مصنفةً حسب الإدارة.') }}</p>
    </div>
</div>

<div class="alert alert-info">
    {{ __('يتم تحديث قيم القسم عند تسجيل دخول المتعلمين. Existing LDAP learners should sign in again to refresh this value.') }}
</div>

{{-- ============================ Filters ============================ --}}
<div class="card shadow mb-4">
    <div class="card-body pr-2">
        <form method="GET" action="{{ route('reports.learning') }}" class="form-row align-items-end">
            <div class="form-group col-md-4">
                <label for="attendance">{{ __('الحضور') }}</label>
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
                <button type="submit" class="btn btn-primary">{{ __('Apply filters') }}</button>
                <a href="{{ route('reports.learning') }}" class="btn btn-light">{{ __('Reset') }}</a>
            </div>
        </form>
    </div>
</div>

{{-- ============================ Course overview ============================ --}}
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="m-0 font-weight-bold text-primary">{{ __('الدورات التدريبية- نظرة عامة') }}</h5>
            <small class="text-muted">{{ __('تم إدراج جميع الدورات التدريبية، بما في ذلك الدورات التي لا تشهد أي نشاط من المتدربين.') }}</small>
        </div>
        <button class="btn btn-md btn-outline-success collapsed" data-toggle="collapse" data-target="#chartOverviewWrap" aria-expanded="false">
            <i class="fas fa-chart-bar "></i> {{ __('رسم بيانى') }}
        </button>
    </div>
    {{-- Chart.js: most viewed / most completed side by side --}}
    <div id="chartOverviewWrap" class="collapse">
        <div class="card-body pr-2">
            <div class="row">
                <div class="col-lg-7 mb-3 mb-lg-0">
                    <h5 class="text-gray-800 font-weight-bold">{{ __('الأكثر مشاهدة') }} <small class="text-muted">({{ __('activity starts') }})</small></h5>
                    <div style="position:relative; height:300px;"><canvas id="chartMostViewed"></canvas></div>
                </div>
                <div class="col-lg-5">
                    <h5 class="text-gray-800 font-weight-bold">{{ __('الأكثر إنهاءً') }} <small class="text-muted">({{ __('completion rate %') }})</small></h5>
                    <div style="position:relative; height:300px;"><canvas id="chartMostCompleted"></canvas></div>
                </div>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-sm mb-0 table-striped table-hover js-learning-report-table">
            <thead class="table-primary">
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
                <tr><td colspan="7" class="text-center text-muted">{{ __('لا توجد دورات تدريبية.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ============================ Summary cards ============================ --}}
<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body pr-2">
                <div class="text-sm font-weight-bold text-primary text-uppercase mb-1">{{ __('المتدربون') }}</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $report['summary']['learners'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body pr-2">
                <div class="text-sm font-weight-bold text-info text-uppercase mb-1">{{ __('المتدربون الحاضرون') }}</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $report['summary']['attending_learners'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body pr-2">
                <div class="text-sm font-weight-bold text-success text-uppercase mb-1">{{ __('متوسط الإنجاز') }}</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $report['summary']['average_progress'] }}%</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body pr-2">
                <div class="text-sm font-weight-bold text-warning text-uppercase mb-1">{{ __('التسجيلات المكتملة') }}</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $report['summary']['completed_enrollments'] }}</div>
            </div>
        </div>
    </div>
</div>

{{-- ============================ Attendance donut + rankings ============================ --}}
<div class="row">
    <div class="col-lg-4 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3">
                <h5 class="m-0 font-weight-bold text-primary">{{ __('حالة الحضور') }}</h5>
                <small class="text-muted">{{ __('Learner/course-term rows matching the filters.') }}</small>
            </div>
            <div class="card-body">
                <div style="position:relative; height:260px;"><canvas id="chartAttendance"></canvas></div>
            </div>
        </div>
    </div>
    <div class="col-lg-4 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3">
                <h5 class="m-0 font-weight-bold text-primary">{{ __('الأكثر مشاهدة') }}</h5>
                <small class="text-muted">{{ __('Ranked by learners who started an activity; activity starts are shown separately.') }}</small>
            </div>
            <div class="table-responsive">
                <table class="table table-sm mb-0 js-learning-report-table table-striped table-hover">
                    <thead  style="background-color: var(--darkteal) !important;" class="table-primary"><tr><th>{{ __('الدورة التدريبية') }}</th><th>{{ __('المتدربون') }}</th><th>{{ __('بدء النشاط') }}</th></tr></thead>
                    <tbody>
                        @forelse($report['most_viewed_courses'] as $course)
                        <tr><td>{{ $course['course_title'] }}</td><td>{{ $course['active_learner_count'] }}</td><td>{{ $course['activity_starts'] }}</td></tr>
                        @empty
                        <tr><td colspan="3" class="text-center text-muted">{{ __('No activity starts match these filters.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3">
                <h5 class="m-0 font-weight-bold text-success">{{ __('الدورات المكتملة') }}</h5>
                <small class="text-muted">{{ __('Ranked by learners who completed every activity in their course term.') }}</small>
            </div>
            <div class="table-responsive">
                <table class="table table-sm mb-0 js-learning-report-table table-striped">
                    <thead class="table-success"><tr><th>{{ __('الدورة التدريبية') }}</th><th>{{ __('الدورات المكتملة') }}</th><th>{{ __('معدل الإكمال') }}</th></tr></thead>
                    <tbody>
                        @forelse($report['most_completed_courses'] as $course)
                        <tr><td>{{ $course['course_title'] }}</td><td>{{ $course['completed_enrollments'] }}</td><td>{{ $course['completion_rate'] }}%</td></tr>
                        @empty
                        <tr><td colspan="3" class="text-center text-muted">{{ __('No completed courses match these filters.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- ============================ Department score rankings ============================ --}}
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="m-0 font-weight-bold text-primary">{{ __('الدرجات حسب البرنامج التدريبى') }}</h5>
            <small class="text-muted">{{ __('   متوسط الدرجات من قِبل المتدربين الذين أكملوا جميع الأنشطة في  الدورة التدريبية.') }}</small>
        </div>
        <button class="btn btn-md btn-outline-success collapsed" data-toggle="collapse" data-target="#chartDeptWrap" aria-expanded="false">
            <i class="fas fa-chart-bar "></i> {{ __('رسم بيانى') }}
        </button>
    </div>
    <div id="chartDeptWrap" class="collapse">
        <div class="card-body">
            <div style="position:relative; height:320px;"><canvas id="chartDepartments"></canvas></div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered mb-0 table-striped table-hover js-learning-report-table">
            <thead class="table-info">
                <tr>
                    <th>{{ __('الإدارة ') }}</th>
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
                    <td>{{ $formatLdapOu($department['name']) }}</td>
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
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h5 class="m-0 font-weight-bold text-primary">{{ __('Learner course details') }}</h5>
        <button class="btn btn-md btn-outline-info collapsed" data-toggle="collapse" data-target="#chartLearnersWrap" aria-expanded="false">
            <i class="fas fa-chart-bar "></i> {{ __('قائمة أفضل 10 متدربين') }}
        </button>
    </div>
    <div id="chartLearnersWrap" class="collapse">
        <div class="card-body">
            <div style="position:relative; height:360px;"><canvas id="chartTopLearners"></canvas></div>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-sm mb-0 table-striped table-hover js-learning-report-table">
            <thead>
                <tr>
                    <th>{{ __('المتدرب') }}</th>
                    <th>{{ __('الإدارة') }}</th>
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
                    <td>{{ $formatLdapOu($row['ldap_ou']) }}</td>
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
@php
    $chartData = [
        'mostViewed' => collect($report['most_viewed_courses'])->map(fn ($c) => [
            'title' => $c['course_title'],
            'starts' => $c['activity_starts'],
            'learners' => $c['active_learner_count'],
        ])->values()->all(),
        'mostCompleted' => collect($report['most_completed_courses'])->map(fn ($c) => [
            'title' => $c['course_title'],
            'rate' => $c['completion_rate'],
            'done' => $c['completed_enrollments'],
            'learners' => $c['learner_count'],
        ])->values()->all(),
        'attendance' => [
            ['label' => __('Not started'), 'value' => collect($report['rows'])->where('attendance_status', 'not_started')->count(), 'color' => '#d1d3e2'],
            ['label' => __('Attending'), 'value' => collect($report['rows'])->where('attendance_status', 'attending')->count(), 'color' => '#36b9cc'],
            ['label' => __('Completed'), 'value' => collect($report['rows'])->where('attendance_status', 'completed')->count(), 'color' => '#1cc88a'],
        ],
        'departments' => collect($report['department_rankings'])->take(10)->map(fn ($d) => [
            'name' => $formatLdapOu($d['name']),
            'learners' => $d['learner_count'],
            'learning' => $d['learning_score'],
            'quiz' => $d['quiz_score'],
        ])->values()->all(),
        'topLearners' => collect($report['rows'])
            ->groupBy('user_id')
            ->map(fn ($r) => [
                'name' => $r->first()['learner_name'],
                'ou' => $formatLdapOu($r->first()['ldap_ou']),
                'score' => round($r->avg('learning_score'), 1),
            ])
            ->sortByDesc('score')->take(10)->values()->all(),
    ];
@endphp
<script>
(function () {
    'use strict';

    if (typeof Chart === 'undefined') {
        console.error('Chart.js failed to load from vendor/chart.js/Chart.min.js');
        return;
    }

    // ---------- Data injected from PHP ----------
   var data = @json($chartData);


    var charts = {};
    var built = {};
    function build(id) {
        if (charts[id] && !built[id]) {
            charts[id]();
            built[id] = true;
        }
        if (id === 'chartOverviewWrap' && charts['chartOverviewWrap2'] && !built['chartOverviewWrap2']) {
            charts['chartOverviewWrap2']();
            built['chartOverviewWrap2'] = true;
        }
    }

    $('#chartOverviewWrap, #chartDeptWrap, #chartLearnersWrap').on('shown.bs.collapse', function (e) {
        build(e.target.id);
    });

    var palette = ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#6f42c1', '#fd7e14', '#20c9a6'];

    // ---------- 1. Most viewed courses (horizontal bars) ----------
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

    }; /* end lazy overview charts */

    // ---------- 2. Most completed courses (doughnut of rates) ----------
    charts['chartOverviewWrap2'] = function () {
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
    };

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
                yAxes: [
                    { id: 'yLearners', position: 'left', ticks: { beginAtZero: true, precision: 0 } },
                    { id: 'yScore', position: 'right', ticks: { beginAtZero: true, max: 100 }, gridLines: { drawOnChartArea: false } }
                ]
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

})();
</script>
<script>
(function () {
    'use strict';

    var labels = {
        previous: @json(__('السابق')),
        next: @json(__('التالي')),
        page: @json(__('صفحة :page من :pages')),
        showing: @json(__('عرض :from إلى :to من :total')),
    };

    document.querySelectorAll('.js-learning-report-table').forEach(function (table) {
        var body = table.tBodies[0];
        if (!body) {
            return;
        }

        var rows = Array.prototype.slice.call(body.rows).filter(function (row) {
            return !Array.prototype.some.call(row.cells, function (cell) {
                return cell.hasAttribute('colspan');
            });
        });
        var pageSize = 10;
        var pageCount = Math.ceil(rows.length / pageSize);

        if (pageCount <= 1) {
            return;
        }

        var currentPage = 0;
        var pagination = document.createElement('nav');
        pagination.className = 'd-flex justify-content-between align-items-center mt-2';
        pagination.setAttribute('aria-label', @json(__('Table pagination')));

        var summary = document.createElement('small');
        summary.className = 'text-muted';
        summary.setAttribute('aria-live', 'polite');

        var controls = document.createElement('div');
        controls.className = 'd-flex align-items-center';

        var previous = document.createElement('button');
        previous.type = 'button';
        previous.className = 'btn btn-sm btn-outline-info';
        previous.textContent = labels.previous;

        var pageLabel = document.createElement('small');
        pageLabel.className = 'text-muted mx-2';
        pageLabel.setAttribute('aria-live', 'polite');

        var next = document.createElement('button');
        next.type = 'button';
        next.className = 'btn btn-sm btn-outline-info';
        next.textContent = labels.next;

        controls.appendChild(previous);
        controls.appendChild(pageLabel);
        controls.appendChild(next);
        pagination.appendChild(summary);
        pagination.appendChild(controls);

        function format(template, values) {
            return template.replace(/:(\w+)/g, function (match, key) {
                return Object.prototype.hasOwnProperty.call(values, key) ? values[key] : match;
            });
        }

        function renderPage() {
            var start = currentPage * pageSize;
            var end = Math.min(start + pageSize, rows.length);

            rows.forEach(function (row, index) {
                row.hidden = index < start || index >= end;
            });

            summary.textContent = format(labels.showing, {
                from: start + 1,
                to: end,
                total: rows.length,
            });
            pageLabel.textContent = format(labels.page, {
                page: currentPage + 1,
                pages: pageCount,
            });
            previous.disabled = currentPage === 0;
            next.disabled = currentPage === pageCount - 1;
        }

        previous.addEventListener('click', function () {
            if (currentPage > 0) {
                currentPage--;
                renderPage();
            }
        });

        next.addEventListener('click', function () {
            if (currentPage < pageCount - 1) {
                currentPage++;
                renderPage();
            }
        });

        var container = table.closest('.table-responsive') || table;
        container.insertAdjacentElement('afterend', pagination);
        renderPage();
    });
})();
</script>
@endpush
