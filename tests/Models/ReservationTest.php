<?php

namespace Tests\Models;

use App\Models\Reservation;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Laravel\Lumen\Testing\WithoutMiddleware;
use Tests\TestCase;

/**
 * Class ReservationTest
 * @package Tests\Models
 */
class ReservationTest extends TestCase
{
    use WithoutMiddleware, DatabaseTransactions;

    /**
     * @covers \App\Models\Reservation::getReservationsFromDate()
     */
    public function testGetReservationsFromDate()
    {
        $records = Reservation::getReservationsFromDate(rand(1,1000), '2020-04-04 00:00:00');

        $this->assertIsArray($records);
    }

    /**
     * @covers \App\Models\Reservation::getReservationsForDate()
     */
    public function testGetReservationsForDate()
    {
        $records = Reservation::getReservationsForDate('2020-04-04 00:00:00');

        $this->assertInstanceOf('Illuminate\Database\Eloquent\Collection', $records);
    }

    /**
     * @covers \App\Models\Reservation::getReservationsForCenterAndPeriod()
     */
    public function testGetReservationsForCenterAndPeriod()
    {
        $records = Reservation::getReservationsForCenterAndPeriod(rand(1,1000), '2020-04-04 00:00:00', '2020-05-04 00:00:00');

        $this->assertIsArray($records);
    }
}
