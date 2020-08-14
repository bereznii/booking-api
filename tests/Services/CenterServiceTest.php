<?php

namespace Tests\Services;

use App\Models\UniqueTests;
use App\Services\CenterService;
use Illuminate\Support\Facades\Cache;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Laravel\Lumen\Testing\WithoutMiddleware;
use Tests\TestCase;

/**
 * Class CenterServiceTest
 * @package Tests\Services
 */
class CenterServiceTest extends TestCase
{
    use WithoutMiddleware, DatabaseTransactions;

    /**
     * @covers \App\Services\CenterService::getCenterUniqueScheduleByDay
     */
    public function testGetCenterUniqueScheduleByDay()
    {
        Cache::forget('unique-centers-ids');
        $centerIds = UniqueTests::getUniqueSpermogramCentersIds();
        $centerSchedule = UniqueTests::getSpermogramCenterSchedule($centerIds[array_rand($centerIds)]);

        $desiredDayOfWeek = rand(0, 6);

        // days of week: 0 -> 6 | sun -> sat
        $daySchedule = CenterService::getCenterUniqueScheduleByDay($desiredDayOfWeek, $centerSchedule);

        $this->assertIsArray($daySchedule);
        $this->assertArrayHasKey('from', $daySchedule);
        $this->assertArrayHasKey('to', $daySchedule);
    }

    /**
     * @covers \App\Services\CenterService::getCentersForSpermogram
     */
    public function testGetCentersForSpermogram()
    {
        $result = (new CenterService())->getCentersForSpermogram();

        $this->assertIsArray($result);
        $this->assertObjectHasAttribute('test', $result[0]);
        $this->assertObjectHasAttribute('centers', $result[0]);

        if (is_object($result[0])) {
            $this->assertIsArray($result[0]->test);
            $this->assertIsArray($result[0]->centers);
        }
    }
}
