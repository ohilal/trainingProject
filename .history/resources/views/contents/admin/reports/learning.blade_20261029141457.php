@extends('layouts.admin')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h1 class="h3 mb-1 text-gray-800">{{ __('Learning report') }}</h1>
        <p class="text-muted mb-0">{{ __('Course attendance, learner progress, activity starts, completions, and scores by LDAP OU.') }}</p>
    </div>
</div>

<div class="alert alert-info">
    {{ __('LDAP OU values are refreshed when learners sign in. Existing LDAP learners should sign in again to refresh this value.') }}
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('reports.learning') }}" class="form-row align-items-end">
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
                <button type="submit" class="btn btn-primary">{{ __('Apply filters') }}</button>
                <a href="{{ route('reports.learning') }}" class="btn btn-light">{{ __('Reset') }}</a>
            </div>
        </form>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">{{ __('Course overview') }}</h6>
        <small class="text-muted">{{ __('All courses are listed, including courses with no learner activity.') }}</small>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-sm mb-0">
            <thead>
                <tr>
                    <th>{{ __('الدورة التدريبية') }}</th>
                    <th>{{ __('البرنامج التدريبى') }}</th>
                    <th>{{ __('المتعلمين') }}</th>
                    <th>{{ __('المتعلمين الذين بدأوا') }}</th>
                    <th>{{ __('متوسط التقدم') }}</th>
                    <th>{{ __('بدء النشاط') }}</th>
                    <th>{{ __('Completed enrollments') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($report['courses'] as $course)
                <tr>
                    <td>{{ $course['course_title'] }}</td>
                    <td>{{ $course['department'] ?? '—' }}</td>
                    <td>{{ $course['learner_count'] }}</td>
                    <td>{{ $course['active_learner_count'] }}</td>
                    <td>{{ $course['average_progress'] }}%</td>
                    <td>{{ $course['activity_starts'] }}</td>
                    <td>{{ $course['completed_enrollments'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="row">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">{{ __('Learners') }}</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $report['summary']['learners'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">{{ __('Attending learners') }}</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $report['summary']['attending_learners'] }}</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-success text-uppercase mb-1">{{ __('Average progress') }}</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $report['summary']['average_progress'] }}%</div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">{{ __('Completed enrollments') }}</div>
                <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $report['summary']['completed_enrollments'] }}</div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">{{ __('Most viewed courses') }}</h6>
                <small class="text-muted">{{ __('Ranked by learners who started an activity; activity starts are shown separately.') }}</small>
            </div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead><tr><th>{{ __('Course') }}</th><th>{{ __('Learners') }}</th><th>{{ __('Activity starts') }}</th></tr></thead>
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
    <div class="col-lg-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-success">{{ __('Most completed courses') }}</h6>
                <small class="text-muted">{{ __('Ranked by learners who completed every activity in their course term.') }}</small>
            </div>
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    <thead><tr><th>{{ __('Course') }}</th><th>{{ __('Completed enrollments') }}</th><th>{{ __('Completion rate') }}</th></tr></thead>
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

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">{{ __('Department score rankings') }}</h6>
        <small class="text-muted">{{ __('Average scores are calculated per learner from completed learning activities and quizzes.') }}</small>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered mb-0">
            <thead>
                <tr>
                    <th>{{ __('LDAP OU') }}</th>
                    <th>{{ __('Learners') }}</th>
                    <th>{{ __('Learning average') }}</th>
                    <th>{{ __('Top learning learner') }}</th>
                    <th>{{ __('Quiz average') }}</th>
                    <th>{{ __('Top quiz learner') }}</th>
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

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">{{ __('Learner course details') }}</h6>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-sm mb-0">
            <thead>
                <tr>
                    <th>{{ __('Learner') }}</th>
                    <th>{{ __('LDAP OU') }}</th>
                    <th>{{ __('Course') }}</th>
                    <th>{{ __('Course department') }}</th>
                    <th>{{ __('Attendance') }}</th>
                    @if($report['selected_activity'] !== null)<th>{{ __('Selected activity') }}</th>@endif
                    <th>{{ __('Progress') }}</th>
                    <th>{{ __('Activity starts') }}</th>
                    <th>{{ __('Learning score') }}</th>
                    <th>{{ __('Quiz score') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($report['rows'] as $row)
                <tr>
                    <td>{{ $row['learner_name'] }}</td>
                    <td>{{ $row['ldap_ou'] }}</td>
                    <td>{{ $row['course_title'] }} <small class="text-muted d-block">{{ $row['term_title'] }}</small></td>
                    <td>{{ $row['course_department'] ?? '—' }}</td>
                    <td>{{ __(ucwords(str_replace('_', ' ', $row['attendance_status']))) }}</td>
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
