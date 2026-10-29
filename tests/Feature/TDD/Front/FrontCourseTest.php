<?php

namespace Tests\Feature\TDD\Front;

use App\Models\Course;
use Tests\BaseTest;

class FrontCourseTest extends BaseTest
{

    protected function setUp() :void
    {
        parent::setUp();
        $this->seed();

        $this->setBaseRoute('front/courses');
        $this->setBaseModel('App\Models\Courses');
 
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_courses_front_shows()
    {
        $this->withoutExceptionHandling();
        
        $response = $this->get('/front/courses');
        $response->assertStatus(200);
    }

    public function test_courses_front_are_ordered_by_most_recently_modified()
    {
        Course::factory()->create([
            'title' => 'Older modified course',
            'updated_at' => now()->subDays(2),
        ]);
        Course::factory()->create([
            'title' => 'Newer modified course',
            'updated_at' => now(),
        ]);

        $content = $this->get('/front/courses')
            ->assertOk()
            ->getContent();

        $newerCoursePosition = strpos($content, 'Newer modified course');
        $olderCoursePosition = strpos($content, 'Older modified course');

        $this->assertNotFalse($newerCoursePosition);
        $this->assertNotFalse($olderCoursePosition);
        $this->assertLessThan($olderCoursePosition, $newerCoursePosition);
    }
}
