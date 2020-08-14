<?php

namespace Tests\Models;

use App\Models\Center;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Laravel\Lumen\Testing\WithoutMiddleware;
use Tests\TestCase;

/**
 * Class CenterTest
 * @package Tests\Models
 */
class CenterTest extends TestCase
{
    use WithoutMiddleware, DatabaseTransactions;

    /**
     * @covers \App\Models\Center::getCenterSchedule
     */
    public function testGetCenterSchedule()
    {
        $ids = Center::all()->pluck('original_id')->toArray();

        $centerSchedule = Center::getCenterSchedule($ids[array_rand($ids)]);

        $this->assertIsArray($centerSchedule);
        $this->assertArrayHasKey('day_t_table', $centerSchedule);
        $this->assertArrayHasKey('fri_t_table', $centerSchedule);
        $this->assertArrayHasKey('sat_t_table', $centerSchedule);
        $this->assertArrayHasKey('sun_t_table', $centerSchedule);
    }
}
