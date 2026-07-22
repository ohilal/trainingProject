<?php

namespace Tests\Feature\TDD\Front;

use App\Models\Department;
use Tests\BaseTest;

class WelcomeTest extends BaseTest
{


    protected function setUp(): void
    {

        parent::setUp();
        $this->seed();
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_homePage_valid()
    {
        $response = $this->get(route('home'));

        $response->assertStatus(200);
    }

    public function test_homePage_shows_all_departments_on_front_page()
    {
        $departments = Department::factory()->count(4)->create();

        $response = $this->get(route('home'));

        $response->assertStatus(200);

        foreach ($departments as $department) {
            $response->assertSeeText($department->title);
        }
    }
}
