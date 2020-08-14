<?php

namespace Tests\Models;

use App\Models\UniqueTests;
use Illuminate\Support\Facades\Cache;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Laravel\Lumen\Testing\WithoutMiddleware;
use Tests\TestCase;

/**
 * Class UniqueTestsTest
 * @package Tests\Models
 */
class UniqueTestsTest extends TestCase
{
    use WithoutMiddleware, DatabaseTransactions;

    /**
     * @covers \App\Models\UniqueTests::getSpermogramTestCodes
     */
    public function testGetSpermogramTestCodes()
    {
        $uniqueTests = UniqueTests::getSpermogramTestCodes();

        $this->assertIsArray($uniqueTests);
    }

    /**
     * @covers \App\Models\UniqueTests::getSpermogramCenters
     */
    public function testGetSpermogramCenters()
    {
        $spermogramCenters = UniqueTests::getSpermogramCenters();

        $this->assertIsArray($spermogramCenters);
    }

    /**
     * @covers \App\Models\UniqueTests::getSpermogramCenterSchedule
     */
    public function testGetSpermogramCenterSchedule()
    {
        Cache::forget('unique-centers-ids');
        $centerIds = UniqueTests::getUniqueSpermogramCentersIds();

        $centerSchedule = UniqueTests::getSpermogramCenterSchedule($centerIds[array_rand($centerIds)]);

        $this->assertIsArray($centerSchedule);
        $this->assertArrayHasKey('centerId', $centerSchedule);
        $this->assertArrayHasKey('centerName', $centerSchedule);
        $this->assertArrayHasKey('day', $centerSchedule);
        $this->assertArrayHasKey('fri', $centerSchedule);
        $this->assertArrayHasKey('sat', $centerSchedule);
        $this->assertArrayHasKey('sun', $centerSchedule);
    }
}
