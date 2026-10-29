@extends('layouts.admin')

@section('content')

<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">{{ __('تقرير التعلم') }}</h1>
    <span class="small text-muted">{{ __('يُحدَّث تلقائياً كل 10 دقائق') }}</span>
</div>

{{-- ============================================================ --}}
{{-- Summary cards                                                --}}
{{-- ============================================================ --}}
@php
    $cards = [
        ['label' => __('الدورات'),               'value' => $summary['courses'],        'color' => 'primary', 'icon' => 'fa-book'],
        ['label' => __('الفصول'),                'value' => $summary['terms'],          'color' => 'info',    'icon' => 'fa-calendar-alt'],
        ['label' => __('المتدربون'),             'value' => $summary['learners'],       'color' => 'success', 'icon' => 'fa-users'],
        ['label' => __('التسجيلات'),             'value' => $summary['enrollments'],    'color' => 'warning', 'icon' => 'fa-user-plus'],
        ['label' => __('متوسط الإنجاز'),         'value' => $summary['avg_progress'] . '%', 'color' => 'danger', 'icon' => 'fa-tasks'],
        ['label' => __('متوسط درجات الاختبارات'),'value' => $summary['avg_quiz_score'], 'color' => 'secondary', 'icon' => 'fa-chart-line'],
    ];
@endphp
<div class="row">
    @foreach($cards as $card)
        <div class="col-xl-2 col-md-6 mb-4">
            <div class="card border-left-{{ $card['color'] }} shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2 ml-0">
                            <div class="text-xs font-weight-bold text-{{ $card['color'] }} text-uppercase mb-1">{{ $card['label'] }}</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $card['value'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas {{ $card['icon'] }} fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

{{-- ============================================================ --}}
{{-- Charts row 1: most viewed / most completed                   --}}
{{-- ============================================================ --}}
<div class="row">
    <div class="col-xl-6 col-lg-7 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 d-flex align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-info">{{ __('الدورات الأكثر مشاهدة (دقائق نشاط)') }}</h6>
                <button type="button" class="btn btn-link btn-sm text-gray-500 p-0" data-toggle="collapse" data-target="#panelMostViewed">
                    <i class="fas fa-chevron-up"></i>
                </button>
            </div>
            <div id="panelMostViewed" class="collapse show">
                <div class="card-body">
                    <div class="position-relative" style="height: 300px;">
                        <canvas id="chartMostViewed"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-6 col-lg-5 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 d-flex align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-success">{{ __('الدورات الأكثر إكمالاً (%)') }}</h6>
                <button type="button" class="btn btn-link btn-sm text-gray-500 p-0" data-toggle="collapse" data-target="#panelMostCompleted">
                    <i class="fas fa-chevron-up"></i>
                </button>
            </div>
            <div id="panelMostCompleted" class="collapse show">
                <div class="card-body">
                    <div class="position-relative" style="height: 300px;">
                        <canvas id="chartMostCompleted"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- Charts row 2: departments learners vs score / top learners   --}}
{{-- ============================================================ --}}
<div class="row">
    <div class="col-xl-7 col-lg-7 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 d-flex align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('الإدارات: عدد المتدربين ومتوسط الدرجات') }}</h6>
                <button type="button" class="btn btn-link btn-sm text-gray-500 p-0" data-toggle="collapse" data-target="#panelDepartments">
                    <i class="fas fa-chevron-up"></i>
                </button>
            </div>
            <div id="panelDepartments" class="collapse show">
                <div class="card-body">
                    <div class="position-relative" style="height: 320px;">
                        <canvas id="chartDepartments"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-5 col-lg-5 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 d-flex align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-warning">{{ __('أفضل المتدربين في الاختبارات') }}</h6>
                <button type="button" class="btn btn-link btn-sm text-gray-500 p-0" data-toggle="collapse" data-target="#panelTopLearners">
                    <i class="fas fa-chevron-up"></i>
                </button>
            </div>
            <div id="panelTopLearners" class="collapse show">
                <div class="card-body">
                    <div class="position-relative" style="height: 320px;">
                        <canvas id="chartTopLearners"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================ --}}
{{-- Ranking lists: most viewed / most completed                  --}}
{{-- ============================================================ --}}
<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('الأكثر مشاهدة') }}</h6>
            </div>
            <div class="card-body">
                @forelse($mostViewed as $course)
                    @php $max = max(1, $mostViewed->max('minutes_watched')); @endphp
                    <div class="d-flex align-items-center mb-3">
                        <div class="flex-grow-1">
                            <div class="small font-weight-bold text-gray-800">{{ $course['title'] }}</div>
                            <div class="progress" style="height: 8px;">
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

{{-- ============================================================ --}}
{{-- Departments (AD OUs) + top learners tables                   --}}
{{-- ============================================================ --}}
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

{{-- ============================================================ --}}
{{-- Courses overview (searchable DataTable)                      --}}
{{-- ============================================================ --}}
<div class="row">
    <div class="col-12 mb-4">
        <div class="card shadow">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('نظرة عامة على الدورات') }}</h6>
                <a href="{{ route('admin.analytics') }}" class="btn btn-sm btn-outline-primary">{{ __('لوحة التحليلات') }}</a>
            </div>
            <div class="card-body table-responsive">
                <table class="table table-sm table-hover" id="learningReportTable">
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
        // ---- Data prepared server-side ------------------------------
        const mostViewedLabels    = @json($mostViewed->pluck('title')->values());
        const mostViewedMinutes   = @json($mostViewed->pluck('minutes_watched')->values());

        const mostCompletedLabels = @json($mostCompleted->pluck('title')->values());
        const mostCompletedPct    = @json($mostCompleted->pluck('progress')->values());
        const mostCompletedDetail = @json($mostCompleted->map(fn ($c) => $c['workouts_done'] . '/' . $c['workouts_total'])->values());

        const deptLabels          = @json($departments->pluck('department')->values());
        const deptLearners        = @json($departments->pluck('learners')->values());
        const deptScores          = @json($departments->map(fn ($d) => $d['avg_score'])->values());

        const learnerLabels       = @json($topLearners->pluck('name')->values());
        const learnerScores       = @json($topLearners->pluck('avg_score')->values());
        const learnerDepts        = @json($topLearners->pluck('department')->values());

        if (!window.Chart) {
            console.error('Chart.js failed to load from vendor/chart.js/Chart.min.js');
            return;
        }

        Chart.defaults.global.defaultFontFamily = "'Nunito', 'Segoe UI', Tahoma, sans-serif";
        Chart.defaults.global.rtl = true;          // RTL tooltips/legends where supported
        Chart.defaults.global.defaultFontColor = '#5a5c69';

        const truncate = (s, n) => (s && s.length > n) ? s.slice(0, n) + '…' : s;

        // ---- 1. Most viewed courses (horizontal bars) ---------------
        new Chart(document.getElementById('chartMostViewed'), {
            type: 'horizontalBar',
            data: {
                labels: mostViewedLabels.map(t => truncate(t, 28)),
                datasets: [{
                    label: @json(__('دقائق النشاط')),
                    data: mostViewedMinutes,
                    backgroundColor: 'rgba(54, 185, 204, 0.7)',
                    borderColor: 'rgb(54, 185, 204)',
                    borderWidth: 1
                }]
            },
            options: {
                maintainAspectRatio: false,
                legend: { display: false },
                scales: {
                    xAxes: [{ ticks: { beginAtZero: true }, gridLines: { color: 'rgba(0,0,0,0.05)' } }],
                    yAxes: [{ gridLines: { display: false } }]
                },
                tooltips: { callbacks: { label: ctx => ' ' + ctx[0].yLabel + ': ' + Number(ctx[0].xLabel).toLocaleString() } }
            }
        });

        // ---- 2. Most completed courses (doughnut of %) --------------
        new Chart(document.getElementById('chartMostCompleted'), {
            type: 'doughnut',
            data: {
                labels: mostCompletedLabels.map(t => truncate(t, 24)),
                datasets: [{
                    data: mostCompletedPct,
                    backgroundColor: [
                        'rgba(46, 204, 113, 0.85)',
                        'rgba(54, 185, 204, 0.85)',
                        'rgba(246, 170, 56, 0.85)',
                        'rgba(113, 85, 211, 0.85)',
                        'rgba(231, 76, 60, 0.85)'
                    ],
                    hoverOffset: 6
                }]
            },
            options: {
                maintainAspectRatio: false,
                cutout: '62%',
                legend: { position: 'bottom', rtl: true, labels: { boxWidth: 12 } },
                tooltips: {
                    callbacks: {
                        label: (ctx, chart) => {
                            const i = ctx.index;
                            return ' ' + chart.data.labels[i] + ': ' + mostCompletedPct[i] + '% (' + mostCompletedDetail[i] + ')';
                        }
                    }
                }
            }
        });

        // ---- 3. Departments: learners vs avg score (mixed axes) -----
        new Chart(document.getElementById('chartDepartments'), {
            type: 'bar',
            data: {
                labels: deptLabels.map(t => truncate(t, 18)),
                datasets: [
                    {
                        label: @json(__('عدد المتدربين')),
                        type: 'bar',
                        data: deptLearners,
                        backgroundColor: 'rgba(78, 115, 223, 0.7)',
                        borderColor: 'rgb(78, 115, 223)',
                        borderWidth: 1,
                        yAxisID: 'y-learners'
                    },
                    {
                        label: @json(__('متوسط الدرجة')),
                        type: 'line',
                        data: deptScores,
                        borderColor: 'rgb(246, 170, 56)',
                        backgroundColor: 'rgba(246, 170, 56, 0.15)',
                        pointBackgroundColor: 'rgb(246, 170, 56)',
                        fill: true,
                        tension: 0.25,
                        yAxisID: 'y-score'
                    }
                ]
            },
            options: {
                maintainAspectRatio: false,
                legend: { position: 'bottom', rtl: true, labels: { boxWidth: 12 } },
                scales: {
                    xAxes: [{ gridLines: { display: false } }],
                    yAxes: [
                        { id: 'y-learners', position: 'left', ticks: { beginAtZero: true, precision: 0 },
                          gridLines: { color: 'rgba(0,0,0,0.05)' },
                          scaleLabel: { display: true, labelString: @json(__('عدد المتدربين')) } },
                        { id: 'y-score', position: 'right', ticks: { beginAtZero: true, max: 100 },
                          gridLines: { drawOnChartArea: false },
                          scaleLabel: { display: true, labelString: @json(__('الدرجة / 100')) } }
                    ]
                }
            }
        });

        // ---- 4. Top learners (horizontal bars) ----------------------
        new Chart(document.getElementById('chartTopLearners'), {
            type: 'horizontalBar',
            data: {
                labels: learnerLabels.map(t => truncate(t, 22)),
                datasets: [{
                    label: @json(__('متوسط الدرجة')),
                    data: learnerScores,
                    backgroundColor: 'rgba(246, 170, 56, 0.7)',
                    borderColor: 'rgb(246, 170, 56)',
                    borderWidth: 1
                }]
            },
            options: {
                maintainAspectRatio: false,
                legend: { display: false },
                scales: {
                    xAxes: [{ ticks: { beginAtZero: true, max: 100 }, gridLines: { color: 'rgba(0,0,0,0.05)' } }],
                    yAxes: [{ gridLines: { display: false } }]
                },
                tooltips: {
                    callbacks: {
                        label: (ctx) => {
                            const dept = learnerDepts[ctx.index] || '-';
                            return ' ' + ctx.yLabel + ': ' + ctx.xLabel + ' — ' + dept;
                        }
                    }
                }
            }
        });

        // ---- Collapsible panels: keep charts sized correctly --------
        document.querySelectorAll('[data-toggle="collapse"]').forEach(btn => {
            const target = document.querySelector(btn.dataset.target);
            if (target) {
                target.addEventListener('shown.bs.collapse', () => window.dispatchEvent(new Event('resize')));
                target.addEventListener('hidden.bs.collapse', () => window.dispatchEvent(new Event('resize')));
            }
        });

        // ---- Searchable courses table (if DataTables is available) --
        if (window.jQuery && jQuery.fn.DataTable) {
            jQuery('#learningReportTable').DataTable({
                pageLength: 25,
                order: [[1, 'desc']],
                language: {
                    search: @json(__('بحث:')),
                    lengthMenu: @json(__('عرض _MENU_ صفاً')),
                    info: @json(__('عرض _START_ إلى _END_ من _TOTAL_ دورة')),
                    infoEmpty: @json(__('لا توجد دورات')),
                    infoFiltered: @json(__('(مصفّاة من _MAX_ دورة)')),
                    zeroRecords: @json(__('لا توجد نتائج مطابقة'))
                }
            });
        }
    })();
</script>
@endpush
