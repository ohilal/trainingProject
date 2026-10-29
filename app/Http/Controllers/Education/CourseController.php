<?php

namespace App\Http\Controllers\Education;

use App\Http\Controllers\Controller;
use App\Http\Requests\CourseRequest;
use App\Models\Author;
use App\Services\Back\Educations\CourseAdminService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{

    protected $service;

    public function __construct(CourseAdminService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function index()
    {
        $this->authorize('course.index');
        $courses = $this->service->index();
        return $this->service->view('index', compact('courses'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function create()
    {
        $this->authorize('course.create');
        return $this->service->view('form', $this->service->create());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CourseRequest $request
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function store(CourseRequest $request)
    {
        $this->authorize('course.create');
        $newAuthorImage = null;

        try {
            DB::transaction(function () use ($request, &$newAuthorImage) {
                $this->service->store($this->courseData($request, $newAuthorImage));
            });
        } catch (\Throwable $exception) {
            if ($newAuthorImage) {
                Storage::disk('public')->delete($newAuthorImage);
            }

            throw $exception;
        }

        return $this->service->redirect();
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $course_id
     * @return \Illuminate\Contracts\View\View|\Illuminate\Contracts\View\Factory
     */
    public function edit($course_id)
    {
        $this->authorize('course.edit');
        return $this->service->view('form', $this->service->edit($course_id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  CourseRequest $request
     * @param  int  $course_id
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function update(CourseRequest $request, $course_id)
    {
        $this->authorize('course.edit');
        $newAuthorImage = null;

        try {
            DB::transaction(function () use ($request, $course_id, &$newAuthorImage) {
                $this->service->update($this->courseData($request, $newAuthorImage), $course_id);
            });
        } catch (\Throwable $exception) {
            if ($newAuthorImage) {
                Storage::disk('public')->delete($newAuthorImage);
            }

            throw $exception;
        }

        return $this->service->redirect('warning');
    }

    private function courseData(CourseRequest $request, ?string &$newAuthorImage): array
    {
        $courseData = $request->validated();
        $authorId = $courseData['author_id'] ?? null;
        $authorSelectionProvided = array_key_exists('author_id', $courseData);

        if (!$authorId && !empty($courseData['author_name'])) {
            if ($request->hasFile('author_image')) {
                $newAuthorImage = $request->file('author_image')->store('course/authors', 'public');

                if ($newAuthorImage === false) {
                    throw new \RuntimeException('Unable to store course author image.');
                }
            }

            $author = Author::create([
                'name' => $courseData['author_name'],
                'image' => $newAuthorImage,
                'bio' => $courseData['author_bio'] ?? null,
                'organization' => $courseData['author_organization'] ?? null,
            ]);
            $authorId = $author->id;
        }

        unset(
            $courseData['author_id'],
            $courseData['author_name'],
            $courseData['author_image'],
            $courseData['author_bio'],
            $courseData['author_organization']
        );

        if ($authorSelectionProvided || $authorId) {
            $courseData['author_id'] = $authorId;
        }

        return $courseData;
    }

    /**
     * Remove the specified course from storage.
     *
     * @param  int  $course_id
     * @return \Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function destroy($course_id)
    {
        $this->authorize('course.delete');
        if ($this->service->destroy($course_id))
            return $this->service->redirect('warning');
    }
}
