<?php

namespace Tests\Controllers;

use App\Jobs\GetDiscountCodeJob;
use App\Jobs\GetWebOrderIdJob;
use App\Models\Reservation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Laravel\Lumen\Testing\WithoutMiddleware;
use Tests\TestCase;

/**
 * Class ReservationControllerTest
 * @package Tests\Controllers
 */
class ReservationControllerTest extends TestCase
{
    use WithoutMiddleware, DatabaseTransactions;

    /**
     * @covers \App\Http\Controllers\ReservationController::getFreeIntervals()
     */
    public function testGetFreeIntervals()
    {
        $paramsQuery = http_build_query([
            'centerId' => 26,
            'startDate' => '2020-07-18',
            'days' => 2
        ]);

        $this->get('api/v1/spermogram/get-free-intervals?' . $paramsQuery);

        $this->assertResponseOk();
        $this->seeJsonStructure($this->getFreeIntervalsJsonStructure());
    }

    /**
     * @covers \App\Http\Controllers\ReservationController::getFreeIntervals()
     */
    public function testGetFreeIntervalsForCenter()
    {
        $paramsQuery = http_build_query([
            'centerId' => 1
        ]);

        $this->get('api/v1/spermogram/get-free-intervals?' . $paramsQuery);

        $this->assertResponseOk();
        $this->seeJsonStructure($this->getFreeIntervalsJsonStructure());
    }

    /**
     * @covers \App\Http\Controllers\ReservationController::storeInterval()
     */
    public function testStoreInterval()
    {
        Queue::fake();
        $body = [
            "middleName" => "Middle Name",
            "lastName" => "Last Name",
            "firstName" => "First Name",
            "doctor"  => "Doctor",
            "email" => "email@mail.com",
            "phone" => "+38(099)888-333-55",
            "testCode" => "4039",
            "sex" => 0,
            "centerId" => 1,
            "appointmentTime" => "2020-03-03 13:30",
            "birthDate" => "1977-02-03"
        ];

        $this->post('api/v1/spermogram/store-reservation', $body);

        $this->seeStatusCode(201);
        $this->seeJsonEquals([
            'success' => true
        ]);

        Queue::assertPushed(GetDiscountCodeJob::class);
        Queue::assertPushed(GetWebOrderIdJob::class);
    }

    /**
     * @covers \App\Http\Controllers\ReservationController::getExistingReservations()
     */
    public function testGetExistingReservations()
    {
        $first = Reservation::orderBy('appointment_time', 'ASC')->limit(1)->get();

        if (isset($first)) {
            $date = $first->first()->appointment_time;
            $date = date('Y-m-d', strtotime($date));
        }

        $paramsQuery = http_build_query([
            'centerId' => 1,
            'startDate' => $date ?? '2020-05-03',
            'days' => 5
        ]);

        $this->get('api/v1/spermogram/get-reservations?' . $paramsQuery);

        $this->assertResponseOk();
        $this->seeJsonStructure($this->getStoredReservationsJsonStructure());
    }

    /**
     * @covers \App\Http\Controllers\ReservationController::getExistingReservationsGrouped()
     */
    public function testGetExistingReservationsGrouped()
    {
        $this->get('api/v1/spermogram/get-grouped-reservations');

        $this->assertResponseOk();
        $this->seeJsonStructure($this->getGroupedReservationsJsonStructure());
    }

    /**
     * @covers \App\Http\Controllers\ReservationController::getExistingReservationsGrouped()
     */
    public function testGetExistingReservationsGroupedForDate()
    {
        $first = Reservation::orderBy('appointment_time', 'ASC')->limit(1)->get();

        if (isset($first)) {
            $date = $first->first()->appointment_time;
            $date = date('Y-m-d', strtotime($date));
        }

        $paramsQuery = http_build_query([
            'date' => $date ?? '2020-06-04'
        ]);

        $this->get('api/v1/spermogram/get-grouped-reservations?' . $paramsQuery);

        $this->assertResponseOk();
        $this->seeJsonStructure($this->getGroupedReservationsJsonStructure());
    }

    /**
     * Return structure of JSON given by /api/v1/spermogram/get-reservations.
     *
     * @return array|\string[][][]
     */
    private function getStoredReservationsJsonStructure(): array
    {
        return [
            'data' => [
                '*' => [
                    'first_name',
                    'last_name',
                    'middle_name',
                    'phone',
                    'birth_date',
                    'sex',
                    'appointment_time',
                    'webOrderId'
                ]
            ]
        ];
    }

    /**
     * Return structure of JSON given by /api/v1/spermogram/get-free-intervals.
     *
     * @return array|\array[][]
     */
    private function getFreeIntervalsJsonStructure(): array
    {
        return [
            'days' => [
                '*' => [
                    'date',
                    'free',
                    'intervals' => [
                        '*' => [
                            'timeFrom', 'timeTo'
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Return structure of JSON given by /api/v1/spermogram/get-grouped-reservations.
     *
     * @return array|\array[][]
     */
    private function getGroupedReservationsJsonStructure(): array
    {
        return [
            'data' => [
                '*' => [
                    'centerId',
                    'reservations' => [
                        '*' => [
                            'first_name',
                            'last_name',
                            'middle_name',
                            'doctor',
                            'card',
                            'email',
                            'phone',
                            'birth_date',
                            'sex',
                            'center_id',
                            'test_code',
                            'appointment_time',
                            'discount_code',
                            'webOrderId'
                        ]
                    ]
                ]
            ]
        ];
    }
}
