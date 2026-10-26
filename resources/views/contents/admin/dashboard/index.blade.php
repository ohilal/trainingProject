@extends('layouts.admin')

@section('content')

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">{{ __('لوحة تحكم المشرفين') }}</h1>
</div>

<!-- Summary cards -->
<div class="row">
    <div class="col-xl-2 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">{{ __('الدورات') }}</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $summary['courses'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">{{ __('الفصول') }}</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $summary['terms'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">{{ __('المتدربون') }}</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $summary['learners'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">{{ __('التسجيلات') }}</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $summary['enrollments'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">{{ __('متوسط الإنجاز') }}</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $summary['avg_progress'] }}%</div>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-6 mb-4">
        <div class="card border-left-secondary shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-gray-600 text-uppercase mb-1">{{ __('متوسط درجات الاختبارات') }}</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $summary['avg_quiz_score'] }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Charts row 1: most viewed / most completed -->
<div class="row">
    <div class="col-xl-6 col-lg-7 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-info">{{ __('الدورات الأكثر مشاهدة (دقائق نشاط)') }}</h6>
            </div>
            <div class="card-body">
                <div class="position-relative" style="height: 300px;">
                    <canvas id="chartMostViewed"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-6 col-lg-5 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-success">{{ __('الدورات الأكثر إكمالاً (%)') }}</h6>
            </div>
            <div class="card-body">
                <div class="position-relative" style="height: 300px;">
                    <canvas id="chartMostCompleted"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts row 2: departments learners vs score / top learners -->
<div class="row">
    <div class="col-xl-7 col-lg-7 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('الإدارات: عدد المتدربين ومتوسط الدرجات') }}</h6>
            </div>
            <div class="card-body">
                <div class="position-relative" style="height: 320px;">
                    <canvas id="chartDepartments"></canvas>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-5 col-lg-5 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-warning">{{ __('أفضل المتدربين في الاختبارات') }}</h6>
            </div>
            <div class="card-body">
                <div class="position-relative" style="height: 320px;">
                    <canvas id="chartTopLearners"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Most viewed courses -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('الأكثر مشاهدة') }}</h6>
            </div>
            <div class="card-body">
                @forelse($mostViewed as $course)
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-grow-1">
                            <div class="small font-weight-bold text-gray-800">{{ $course['title'] }}</div>
                            <div class="progress" style="height: 8px;">
                                @php $max = max(1, $mostViewed->max('minutes_watched')); @endphp
                                <div class="progress-bar bg-info" role="progressbar"
                                     style="width: {{ round($course['minutes_watched'] / $max * 100) }}%"></div>
                            </div>
                            <div class="small text-muted">{{ number_format($course['minutes_watched']) }} {{ __('دقيقة نشاط') }}</div>
                        </div>
                    </div>
                @empty
                    <p class="text-muted mb-0">{{ __('لا توجد بيانات بعد') }}</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Most completed courses -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-success">{{ __('الأكثر إكمالاً') }}</h6>
            </div>
            <div class="card-body">
                @forelse($mostCompleted as $course)
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-grow-1">
                            <div class="small font-weight-bold text-gray-800">{{ $course['title'] }}</div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-success" role="progressbar"
                                     style="width: {{ $course['progress'] }}%"></div>
                            </div>
                            <div class="small text-muted">{{ $course['progress'] }}% ({{ $course['workouts_done'] }}/{{ $course['workouts_total'] }})</div>
                        </div>
                    </div>
                @empty
                    <p class="text-muted mb-0">{{ __('لا توجد بيانات بعد') }}</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Departments (AD OUs) -->
<div class="row">
    <div class="col-lg-7 mb-4">
        <div class="card shadow">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('إدارات Active Directory (OU)') }}</h6>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-sm table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('الإدارة') }}</th>
                            <th class="text-center">{{ __('المتدربون') }}</th>
                            <th class="text-center">{{ __('نسبة الإنجاز') }}</th>
                            <th class="text-center">{{ __('متوسط الدرجة') }}</th>
                            <th>{{ __('الأعلى تقييماً') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($departments as $dept)
                            <tr>
                                <td class="font-weight-bold">{{ $dept['department'] }}</td>
                                <td class="text-center">{{ $dept['learners'] }}</td>
                                <td class="text-center">
                                    <div class="progress" style="height: 6px; min-width: 60px;">
                                        <div class="progress-bar bg-warning" style="width: {{ $dept['progress'] }}%"></div>
                                    </div>
                                    <span class="small text-muted">{{ $dept['progress'] }}%</span>
                                </td>
                                <td class="text-center {{ $dept['avg_score'] !== null && $dept['avg_score'] >= 80 ? 'text-success font-weight-bold' : '' }}">
                                    {{ $dept['avg_score'] ?? '-' }}
                                </td>
                                <td class="small">
                                    @foreach($dept['top_learners'] as $learner)
                                        <div>{{ $learner['name'] }} — {{ round($learner['score'], 1) }}</div>
                                    @endforeach
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-muted text-center">{{ __('لا يوجد متدربون مرتبطون بإدارات AD بعد. تظهر الإدارة تلقائياً عند تسجيل الدخول عبر LDAP.') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Top learners -->
    <div class="col-lg-5 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-info">{{ __('أفضل المتدربين في الاختبارات') }}</h6>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('الاسم') }}</th>
                            <th>{{ __('الإدارة') }}</th>
                            <th class="text-center">{{ __('الدرجة') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($topLearners as $i => $learner)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td>{{ $learner['name'] }}</td>
                                <td class="small text-muted">{{ $learner['department'] }}</td>
                                <td class="text-center font-weight-bold">{{ $learner['avg_score'] }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-muted text-center">{{ __('لا توجد نتائج اختبارات بعد') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Courses overview -->
<div class="row">
    <div class="col-12 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('نظرة عامة على الدورات') }}</h6>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-sm table-hover">
                    <thead>
                        <tr>
                            <th>{{ __('الدورة') }}</th>
                            <th class="text-center">{{ __('الحضور') }}</th>
                            <th class="text-center">{{ __('المهام المكتملة') }}</th>
                            <th class="text-center">{{ __('نسبة التقدم') }}</th>
                            <th class="text-center">{{ __('دقائق النشاط') }}</th>
                            <th class="text-center">{{ __('متوسط درجة الاختبار') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($courseStats as $course)
                            <tr>
                                <td class="font-weight-bold">{{ $course['title'] }}</td>
                                <td class="text-center">{{ $course['attendees'] }}</td>
                                <td class="text-center">{{ $course['workouts_done'] }} / {{ $course['workouts_total'] }}</td>
                                <td class="text-center">
                                    <div class="progress" style="height: 6px; min-width: 80px;">
                                        <div class="progress-bar {{ $course['progress'] >= 70 ? 'bg-success' : ($course['progress'] >= 40 ? 'bg-warning' : 'bg-danger') }}"
                                             style="width: {{ $course['progress'] }}%"></div>
                                    </div>
                                    <span class="small text-muted">{{ $course['progress'] }}%</span>
                                </td>
                                <td class="text-center">{{ number_format($course['minutes_watched']) }}</td>
                                <td class="text-center">{{ $course['avg_quiz_score'] ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-muted text-center">{{ __('لا توجد دورات بعد') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@push('js')
<script src="{{ URL::to('vendor/chart.js/Chart.min.js') }}"></script>
<script>
    (function () {
        // RTL-friendly defaults for Arabic labels
        if (window.Chart) {
            Chart.defaults.global.defaultFontFamily = "'Nunito', 'Segoe UI', Tahoma, sans-serif";
        }

        var palette = {
            blue:   'rgba(58, 122, 234, 0.75)',
            green:  'rgba(28, 200, 138, 0.75)',
            yellow: 'rgba(246, 194, 62, 0.8)',
            red:    'rgba(231, 74, 74, 0.7)',
            teal:   'rgba(54, 185, 204, 0.75)'
        };

        function truncate(label, max) {
            max = max || 22;
            return label && label.length > max ? label.substring(0, max - 1) + '…' : (label || '-');
        }

        /* ---- Most viewed courses (horizontal bars) ---- */
        var mostViewed = @json($mostViewed->map(fn ($c) => ['title' => truncate($c['title']), 'value' => (int) $c['minutes_watched']])->values());
        new Chart(document.getElementById('chartMostViewed'), {
            type: 'horizontalBar',
            data: {
                labels: mostViewed.map(function (d) { return d.title; }),
                datasets: [{
                    label: @json(__('دقائق النشاط')),
                    data: mostViewed.map(function (d) { return d.value; }),
                    backgroundColor: palette.teal,
                    borderColor: 'rgba(54, 185, 204, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                maintainAspectRatio: false,
                legend: { display: false },
                scales: { xAxes: [{ ticks: { beginAtZero: true } }] }
            }
        });

        /* ---- Most completed courses (% progress) ---- */
        var mostCompleted = @json($mostCompleted->map(fn ($c) => ['title' => truncate($c['title']), 'value' => (float) $c['progress']])->values());
        new Chart(document.getElementById('chartMostCompleted'), {
            type: 'doughnut',
            data: {
                labels: mostCompleted.map(function (d) { return d.title; }),
                datasets: [{
                    data: mostCompleted.map(function (d) { return d.value; }),
                    backgroundColor: [palette.blue, palette.green, palette.yellow, palette.red, palette.teal],
                    hoverOffset: 6
                }]
            },
            options: {
                maintainAspectRatio: false,
                cutoutPercentage: 55,
                legend: { position: 'bottom', labels: { boxWidth: 12 } },
                tooltips: {
                    callbacks: {
                        label: function (item, data) {
                            return ' ' + data.labels[item.index] + ': ' + item.yLabel + '%';
                        }
                    }
                }
            }
        });

        /* ---- Departments: learners (bars) vs avg score (line, 2nd axis) ---- */
        var departments = @json($departments->take(10)->values()->map(fn ($d) => [
            'name'    => truncate($d['department'], 16),
            'learners'=> (int) $d['learners'],
            'score'   => $d['avg_score'] !== null ? (float) $d['avg_score'] : null,
            'progress'=> (float) $d['progress'],
        ]));
        new Chart(document.getElementById('chartDepartments'), {
            type: 'bar',
            data: {
                labels: departments.map(function (d) { return d.name; }),
                datasets: [
                    {
                        label: @json(__('عدد المتدربين')),
                        data: departments.map(function (d) { return d.learners; }),
                        backgroundColor: palette.blue,
                        yAxisID: 'y-learners'
                    },
                    {
                        label: @json(__('متوسط الدرجة')),
                        type: 'line',
                        data: departments.map(function (d) { return d.score; }),
                        borderColor: 'rgba(231, 74, 74, 1)',
                        backgroundColor: palette.red,
                        fill: false,
                        pointRadius: 4,
                        steppedLine: false,
                        yAxisID: 'y-score'
                    }
                ]
            },
            options: {
                maintainAspectRatio: false,
                scales: {
                    yAxes: [
                        { id: 'y-learners', position: 'left', ticks: { beginAtZero: true, precision: 0 }, scaleLabel: { display: true, labelString: @json(__('عدد المتدربين')) } },
                        { id: 'y-score', position: 'right', ticks: { beginAtZero: true, max: 100 }, gridLines: { drawOnChartArea: false }, scaleLabel: { display: true, labelString: @json(__('الدرجة / 100')) } }
                    ]
                },
                legend: { position: 'bottom' }
            }
        });

        /* ---- Top learners in quizzes ---- */
        var topLearners = @json($topLearners->map(fn ($l) => ['name' => truncate($l['name'], 20), 'score' => (float) $l['avg_score']])->values());
        new Chart(document.getElementById('chartTopLearners'), {
            type: 'horizontalBar',
            data: {
                labels: topLearners.map(function (d) { return d.name; }),
                datasets: [{
                    label: @json(__('متوسط درجة الاختبار')),
                    data: topLearners.map(function (d) { return d.score; }),
                    backgroundColor: palette.yellow,
                    borderColor: 'rgba(246, 194, 62, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                maintainAspectRatio: false,
                legend: { display: false },
                scales: { xAxes: [{ ticks: { beginAtZero: true, max: 100 } }] }
            }
        });
    })();
</script>
@endpush
