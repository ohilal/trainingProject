<?php

namespace Tests\Feature\TDD;


use App\Models\Author;
use App\Models\Course;
use App\Models\Department;
use App\Http\Requests\DepartmentRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\BaseTest;

class CourseTest extends BaseTest
{
    protected function setUp() :void
    {
        parent::setUp();
        $this->seed();

        $this->setBaseRoute('course');
        $this->setBaseModel('App\Models\Course');

        
    }



    /**
     * A basic test to check access level controller with without access to page
     *
     * @return void
     */
    public function test_acl()
    {
        $this->withOutPermissionUser();
        $this->withOutAccessLevel();
    }


    /**
     * A basic test to validation is worked.
     *
     * @return void
     */
    public function test_validation()
    {
        $this->setValidationRules((new DepartmentRequest())->rules());
        $this->signIn();
        $this->validation();
    }



    /**
     * A basic test to create form is worked correctly.
     *
     * @return void
     */
    public function test_create_form()
    {
        $this->signIn();
        $this->create();
    }

    /**
     * A basic test to update method with authenticated verfied.
     *
     * @return void
     */
    public function test_update_form()
    {
        $this->signIn();
        $this->update();
    }

    public function test_course_can_create_and_reuse_an_author()
    {
        $this->signIn();
        Storage::fake('public');
        $department = Department::query()->firstOrFail();

        $courseData = [
            'title' => 'Course with a new author',
            'description' => 'Course description',
            'image' => 'course.png',
            'department_id' => $department->id,
            'author_name' => 'Reusable Author',
            'author_image' => UploadedFile::fake()->image('author.png'),
            'author_bio' => 'Author biography',
            'author_organization' => 'Training Organization',
        ];

        $this->post(route('course.store'), $courseData)->assertRedirect();

        $author = Author::where('name', 'Reusable Author')->firstOrFail();
        $this->assertDatabaseHas('authors', [
            'id' => $author->id,
            'bio' => 'Author biography',
            'organization' => 'Training Organization',
        ]);
        $this->assertDatabaseHas('courses', [
            'title' => 'Course with a new author',
            'author_id' => $author->id,
        ]);
        Storage::disk('public')->assertExists($author->image);

        $this->post(route('course.store'), [
            'title' => 'Course with an existing author',
            'description' => 'Another course description',
            'image' => 'another-course.png',
            'department_id' => $department->id,
            'author_id' => $author->id,
        ])->assertRedirect();

        $this->assertDatabaseHas('courses', [
            'title' => 'Course with an existing author',
            'author_id' => $author->id,
        ]);
        $this->assertSame(1, Author::where('name', 'Reusable Author')->count());
    }



    /**
     * A basic test to delete method and response correctly.
     *
     * @return void
     */
    public function test_delete_form()
    {
        $this->signIn();
        $this->destroy();
    }
}
