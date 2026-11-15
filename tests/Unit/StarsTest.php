<?php

namespace Tests\Unit;

use App\View\Components\Atoms\Stars;
use Tests\TestCase;

class StarsTest extends TestCase
{
    public function test_score_maps_to_five_stars_in_half_star_increments(): void
    {
        $cases = [
            0 => [0, 0, 5],
            10 => [0, 1, 4],
            20 => [1, 0, 4],
            70 => [3, 1, 1],
            100 => [5, 0, 0],
            -10 => [0, 0, 5],
            110 => [5, 0, 0],
        ];

        foreach ($cases as $score => [$full, $half, $empty]) {
            $html = (string) $this->component(Stars::class, ['score' => $score]);

            $this->assertSame(5, substr_count($html, '<i '), "Score {$score} should render five stars.");
            $this->assertSame($full, substr_count($html, 'class="fa fa-star"'), "Unexpected full stars for score {$score}.");
            $this->assertSame($half, substr_count($html, 'class="fa fa-star-half-alt"'), "Unexpected half stars for score {$score}.");
            $this->assertSame($empty, substr_count($html, 'class="fa fa-star text-secondary"'), "Unexpected empty stars for score {$score}.");
        }
    }
}
