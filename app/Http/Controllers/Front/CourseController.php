<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Services\Front\CourseServices;
use App\Services\Front\HomeServices;
use App\Models\Department;
use App\Models\Course;
use App\Models\File;
use App\Models\Term;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    private const LEARNER_ROLE_ID = 4;

    protected $homeService;
    protected $courseService;

    public function __construct(HomeServices $homeService, CourseServices $courseService)
    {
        $this->homeService = $homeService;
        $this->courseService = $courseService;
    }

    /**
     * Display a listing of the Courses.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function courses()
    {
        $homeCompactReturn = $this->homeService->homeIndex();
        $homeCompactReturn['department'] = null;

        return view('contents.front.courses.index', $homeCompactReturn);
    }



    /**
     * Display a listing of the resource.
     * @param int $id
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function course($id)
    {
        $courseCompactData = $this->courseService->getCourseInfo($id);
        $user = auth()->user();
        $userId = $user ? $user->id : null;
        $course = $courseCompactData['course'];
        $isAssignedToCourse = $userId !== null
            && $course->Terms()
                ->whereHas('Participants', function ($query) use ($userId) {
                    $query->where('users.id', $userId);
                })
                ->exists();
        $hasIncompleteSelfEnrollment = false;

        if ($userId !== null && $course->allow_self_enrollment) {
            $termCount = $course->Terms()->count();
            $assignedLearnerTermCount = $course->Terms()
                ->whereHas('Participants', function ($query) use ($userId) {
                    $query->where('users.id', $userId)
                        ->where('term_user.role_id', self::LEARNER_ROLE_ID);
                })
                ->count();

            $hasIncompleteSelfEnrollment = $assignedLearnerTermCount > 0
                && $assignedLearnerTermCount < $termCount;
        }

        return view('contents.front.courses.course', array_merge(
            $courseCompactData,
            compact('isAssignedToCourse', 'hasIncompleteSelfEnrollment')
        ));
    }

    public function termSessions(Course $course, Term $term)
    {
        abort_unless((int) $term->course_id === (int) $course->id, 404);
        abort_unless((bool) $term->is_published, 404);

        $term->load('Sessions');

        return view('contents.front.courses.term-sessions', compact('course', 'term'));
    }

    public function join(Course $course)
    {
        abort_unless((bool) $course->is_published, 404);
        abort_unless($course->allow_self_enrollment, 403);

        $terms = $course->Terms()->get();

        if ($terms->isEmpty()) {
            return redirect()
                ->route('front.course', $course->id)
                ->with('warning', __('There are no published terms to join yet.'));
        }

        $userId = auth()->user()->id;
        foreach ($terms as $term) {
            $term->Participants()->syncWithoutDetaching([
                $userId => ['role_id' => self::LEARNER_ROLE_ID],
            ]);
        }

        $publishedTerm = $terms->first(function (Term $term) {
            return (bool) $term->is_published;
        });

        if (!$publishedTerm) {
            return redirect()
                ->route('front.course', $course->id)
                ->with('warning', __('You have joined the course, but there are no published terms available yet.'));
        }

        return redirect()
            ->route('front.term.sessions', [
                'course' => $course->id,
                'term' => $publishedTerm->id,
            ])
            ->with('success', __('You have joined the course.'));
    }

    public function file(string $path)
    {
        $file = File::where('file', $path)->firstOrFail();
        $user = auth()->user();

        $terms = Term::where('is_published', true)
            ->whereHas('Course', function ($query) {
                $query->where('is_published', true);
            })
            ->where(function ($query) use ($file) {
                $query->whereHas('Sessions.Files', function ($fileQuery) use ($file) {
                    $fileQuery->where('files.id', $file->id);
                })->orWhereHas('Sessions.Documents.Files', function ($fileQuery) use ($file) {
                    $fileQuery->where('files.id', $file->id);
                });
            })
            ->with('Course')
            ->get();

        if ($user->can('file.index')) {
            abort_unless(Storage::disk('public')->exists($file->file), 404);

            return Storage::disk('public')->response($file->file);
        }

        foreach ($terms as $term) {
            if ($term->Participants()
                ->where('users.id', $user->id)
                ->wherePivot('role_id', self::LEARNER_ROLE_ID)
                ->exists()) {
                abort_unless(Storage::disk('public')->exists($file->file), 404);

                return Storage::disk('public')->response($file->file);
            }
        }

        $openCourseTerm = $terms->first(function (Term $term) {
            return $term->Course->allow_self_enrollment;
        });

        if ($openCourseTerm) {
            return redirect()
                ->route('front.course', $openCourseTerm->course_id)
                ->with('warning', __('Join this course to access its files.'));
        }

        abort(403);
    }



    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function plans()
    {

        $plans = $this->courseService->getPlanPageInfo();
        return view(
            'contents.front.plans.index',
            $plans
        );
    }

// public function byDepartment($id)
// {
//     $department = \App\Models\Department::with('courses')->findOrFail($id);
//     $courses = $department->courses;
//     return view('contents.front.courses.index', compact('department', 'courses'));
// }
public function byDepartment($id)
{
    $department = Department::findOrFail($id);
    $courses = $department->courses;
    
    // Fetch all departments for the menu
    $departments = Department::all(); 

    return view('contents.front.courses.index', compact('department', 'courses', 'departments'));
}
}
