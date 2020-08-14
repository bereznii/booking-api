<?php

namespace Tests\Services;

use App\Jobs\GetDiscountCodeJob;
use App\Jobs\GetWebOrderIdJob;
use App\Models\Reservation;
use App\Services\SpermogramReservationService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Queue;
use Laravel\Lumen\Testing\DatabaseTransactions;
use Laravel\Lumen\Testing\WithoutMiddleware;
use ReflectionException;
use Tests\TestCase;

/**
 * Class SpermogramReservationServiceTest
 * @package Tests\Services
 */
class SpermogramReservationServiceTest extends TestCase
{
    use WithoutMiddleware, DatabaseTransactions;

    /**
     * @coversNothing
     * @param $object
     * @param $methodName
     * @param array $parameters
     * @return mixed
     * @throws ReflectionException
     */
    public function invokeMethod($object, $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $parameters);
    }

    /**
     * @covers \App\Services\SpermogramReservationService::prepareReservationData()
     */
    public function testPrepareReservationData()
    {
        //stubs
        $requestArray = $this->getRequestForStoringIntervalStub();

        $request = new Request($requestArray);
        $result = (new SpermogramReservationService())->prepareReservationData($request);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('middle_name', $result);
        $this->assertArrayHasKey('first_name', $result);
        $this->assertArrayHasKey('last_name', $result);
        $this->assertArrayHasKey('doctor', $result);
        $this->assertArrayHasKey('client_card', $result);
        $this->assertArrayHasKey('email', $result);
        $this->assertArrayHasKey('phone', $result);
        $this->assertArrayHasKey('sex', $result);
        $this->assertArrayHasKey('center_original_id', $result);
        $this->assertArrayHasKey('test_code', $result);
        $this->assertArrayHasKey('appointment_time', $result);
        $this->assertArrayHasKey('birth_date', $result);
    }

    /**
     * @covers \App\Services\SpermogramReservationService::getReservedAppointmentDates()
     * @throws ReflectionException
     */
    public function testGetReservedAppointmentDates()
    {
        $obj = new SpermogramReservationService();

        $result = $this->invokeMethod($obj, 'getReservedAppointmentDates', [1, '2020-04-25 14:14:14']);

        $this->assertIsArray($result);

        if (!empty($result)) {
            $this->assertArrayHasKey('appointment_time', $result[0]);
        }
    }

    /**
     * @covers \App\Services\SpermogramReservationService::getIntervals()
     */
    public function testGetIntervals()
    {
        $requestArray = $this->getRequestForGettingIntervalsStub();

        $request = new Request($requestArray);
        $intervals = (new SpermogramReservationService())->getIntervals($request);

        $this->assertIsArray($intervals);
        $this->assertArrayHasKey('days', $intervals);
    }

    /**
     * @covers \App\Services\SpermogramReservationService::getIntervals()
     */
    public function testGetIntervalsForCenter()
    {
        $requestArray = $this->getRequestForGettingIntervalsForCenterStub();

        $request = new Request($requestArray);
        $intervals = (new SpermogramReservationService())->getIntervals($request);

        $this->assertIsArray($intervals);
        $this->assertArrayHasKey('days', $intervals);
    }

    /**
     * @covers \App\Services\SpermogramReservationService::getFreeIntervalsInDatesRange
     */
    public function testGetFreeIntervalsInDatesRange()
    {
        //stubs
        $existingReservationsStub = $this->getExistingReservationsStub();
        $centerScheduleStub = $this->getCenterScheduleStub();

        $days = rand(0, 10);
        $dateFrom = Carbon::now()->format('Y-m-d');
        $dateTo = date('Y-m-d', strtotime("{$dateFrom} + {$days} day"));
        $period = CarbonPeriod::create($dateFrom, $dateTo);

        $obj = new SpermogramReservationService();
        $intervals = $this->invokeMethod($obj, 'getFreeIntervalsInDatesRange', [$period, $centerScheduleStub, $existingReservationsStub]);

        $this->assertIsArray($intervals);
        $this->assertArrayHasKey('date', $intervals[0]);
        $this->assertArrayHasKey('free', $intervals[0]);
        $this->assertArrayHasKey('intervals', $intervals[0]);
    }

    /**
     * @covers \App\Services\SpermogramReservationService::getIntervalsForTimeRange()
     */
    public function testGetIntervalsForTimeRange()
    {
        //stubs
        $existingReservationsStub = $this->getExistingReservationsStub();
        $dayScheduleStub = $this->getDayScheduleStub();
        $dayOffScheduleStub = $this->getDayOffScheduleStub();

        $obj = new SpermogramReservationService();

        $intervals = $this->invokeMethod($obj, 'getIntervalsForTimeRange', [Carbon::now()->format('Y-m-d'), $dayScheduleStub, $existingReservationsStub]);
        $intervalsDayOff = $this->invokeMethod($obj, 'getIntervalsForTimeRange', [Carbon::now()->format('Y-m-d'), $dayOffScheduleStub, $existingReservationsStub]);

        $this->assertIsArray($intervals);
        $this->assertEmpty($intervalsDayOff);
        $this->assertArrayHasKey('timeFrom', $intervals[0]);
        $this->assertArrayHasKey('timeTo', $intervals[0]);
    }

    /**
     * @covers \App\Services\SpermogramReservationService::storeReservation()
     */
    public function testStoreReservation()
    {
        Queue::fake();

        //stubs
        $requestArray = $this->getReservationStub();

        $request = new Request($requestArray);
        $result = (new SpermogramReservationService())->storeReservation($request);

        $this->assertTrue($result);

        Queue::assertPushed(GetDiscountCodeJob::class);
        Queue::assertPushed(GetWebOrderIdJob::class);
    }

    /**
     * @todo refactor
     * @covers \App\Services\SpermogramReservationService::getReservations()
     */
    public function testGetReservationsForCenter()
    {
        $centerId = 26;
        $requestArray = [
            "centerId" => $centerId,
        ];

        $request = new Request($requestArray);
        $result = (new SpermogramReservationService())->getReservations($request);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);

        if (!empty($result['data'])) {
            $this->assertArrayHasKey('middle_name', $result['data'][0]);
            $this->assertArrayHasKey('first_name', $result['data'][0]);
            $this->assertArrayHasKey('last_name', $result['data'][0]);
            $this->assertArrayHasKey('doctor', $result['data'][0]);
            $this->assertArrayHasKey('card', $result['data'][0]);
            $this->assertArrayHasKey('email', $result['data'][0]);
            $this->assertArrayHasKey('phone', $result['data'][0]);
            $this->assertArrayHasKey('sex', $result['data'][0]);
            $this->assertArrayHasKey('center_id', $result['data'][0]);
            $this->assertArrayHasKey('test_code', $result['data'][0]);
            $this->assertArrayHasKey('appointment_time', $result['data'][0]);
            $this->assertArrayHasKey('birth_date', $result['data'][0]);
            $this->assertArrayHasKey('discount_code', $result['data'][0]);
            $this->assertArrayHasKey('webOrderId', $result['data'][0]);

            //check if tomorrow
            if (isset($result['data'][0]['appointment_time'])) {
                $this->assertSame(date('Y-m-d', strtotime('+1 day')), date('Y-m-d', strtotime($result['data'][0]['appointment_time'])));

                $last = Arr::last($result['data']);
                $this->assertSame(date('Y-m-d', strtotime('+1 day')), date('Y-m-d', strtotime($last['appointment_time'])));
            }

            if (isset($result['data'][0]['center_id'])) {
                $this->assertSame($centerId, $result['data'][0]['center_id']);
            }
        }
    }

    /**
     * @todo refactor
     * @covers \App\Services\SpermogramReservationService::getReservations()
     */
    public function testGetReservationsWithDate()
    {
        $first = Reservation::orderBy('appointment_time', 'ASC')->limit(1)->get();

        $date = '2020-05-05 00:00:00';
        if (isset($first)) {
            $first = $first->first();
            $date = isset($first) ? $first->appointment_time : '2020-05-05 00:00:00';
        }

        $requestArray = [
            "centerId" => 26,
            'date' => $date
        ];

        $request = new Request($requestArray);
        $result = (new SpermogramReservationService())->getReservations($request);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('data', $result);

        if (!empty($result['data'])) {
            $this->assertArrayHasKey('middle_name', $result['data'][0]);
            $this->assertArrayHasKey('first_name', $result['data'][0]);
            $this->assertArrayHasKey('last_name', $result['data'][0]);
            $this->assertArrayHasKey('doctor', $result['data'][0]);
            $this->assertArrayHasKey('card', $result['data'][0]);
            $this->assertArrayHasKey('email', $result['data'][0]);
            $this->assertArrayHasKey('phone', $result['data'][0]);
            $this->assertArrayHasKey('sex', $result['data'][0]);
            $this->assertArrayHasKey('center_id', $result['data'][0]);
            $this->assertArrayHasKey('test_code', $result['data'][0]);
            $this->assertArrayHasKey('appointment_time', $result['data'][0]);
            $this->assertArrayHasKey('birth_date', $result['data'][0]);
            $this->assertArrayHasKey('discount_code', $result['data'][0]);
            $this->assertArrayHasKey('webOrderId', $result['data'][0]);

            if (isset($result['data'][0]['appointment_time'])) {
                $this->assertSame(date('Y-m-d', strtotime('+1 day')), date('Y-m-d', strtotime($result['data'][0]['appointment_time'])));

                $last = Arr::last($result['data']);
                $this->assertSame(date('Y-m-d', strtotime('+1 day')), date('Y-m-d', strtotime($last['appointment_time'])));
            }
        }
    }

    /**
     * @covers \App\Services\SpermogramReservationService::getExistingReservationsGrouped()
     */
    public function testGetExistingReservationsGrouped()
    {
        $requestArray = [];

        $request = new Request($requestArray);
        $result = (new SpermogramReservationService())->getExistingReservationsGrouped($request);

        $this->assertIsArray($result);

        if (!empty($result['data'])) {
            $this->assertArrayHasKey('centerId', $result['data'][0]);
            $this->assertArrayHasKey('reservations', $result['data'][0]);

            if (!empty($result['data'][0]['reservations'])) {
                $this->assertArrayHasKey('middle_name', $result['data'][0]['reservations'][0]);
                $this->assertArrayHasKey('first_name', $result['data'][0]['reservations'][0]);
                $this->assertArrayHasKey('last_name', $result['data'][0]['reservations'][0]);
                $this->assertArrayHasKey('doctor', $result['data'][0]['reservations'][0]);
                $this->assertArrayHasKey('card', $result['data'][0]['reservations'][0]);
                $this->assertArrayHasKey('email', $result['data'][0]['reservations'][0]);
                $this->assertArrayHasKey('phone', $result['data'][0]['reservations'][0]);
                $this->assertArrayHasKey('sex', $result['data'][0]['reservations'][0]);
                $this->assertArrayHasKey('center_id', $result['data'][0]['reservations'][0]);
                $this->assertArrayHasKey('test_code', $result['data'][0]['reservations'][0]);
                $this->assertArrayHasKey('appointment_time', $result['data'][0]['reservations'][0]);
                $this->assertArrayHasKey('birth_date', $result['data'][0]['reservations'][0]);
                $this->assertArrayHasKey('discount_code', $result['data'][0]['reservations'][0]);
                $this->assertArrayHasKey('webOrderId', $result['data'][0]['reservations'][0]);

                if (isset($result['data'][0]['reservations'][0])) {
                    $this->assertSame(date('Y-m-d', strtotime('+1 day')), date('Y-m-d', strtotime($result['data'][0]['reservations'][0]['appointment_time'])));

                    $last = Arr::last($result['data'][0]['reservations']);

                    $this->assertSame(date('Y-m-d', strtotime('+1 day')), date('Y-m-d', strtotime($last['appointment_time'])));
                }
            }
        }
    }

    /**
     * @covers \App\Services\SpermogramReservationService::getExistingReservationsGrouped()
     */
    public function testGetExistingReservationsGroupedWithDate()
    {
        $first = Reservation::orderBy('appointment_time', 'ASC')->limit(1)->get();

        $date = '2020-05-05 00:00:00';
        if (isset($first)) {
            $first = $first->first();
            $date = isset($first) ? $first->appointment_time : '2020-05-05 00:00:00';
        }

        $requestArray = [
            'date' => $date
        ];

        $request = new Request($requestArray);
        $result = (new SpermogramReservationService())->getExistingReservationsGrouped($request);

        $this->assertIsArray($result);

        if (!empty($result['data'])) {
            $this->assertArrayHasKey('centerId', $result['data'][0]);
            $this->assertArrayHasKey('reservations', $result['data'][0]);

            if (!empty($result['data'][0]['reservations'])) {
                $this->assertArrayHasKey('middle_name', $result['data'][0]['reservations'][0]);
                $this->assertArrayHasKey('first_name', $result['data'][0]['reservations'][0]);
                $this->assertArrayHasKey('last_name', $result['data'][0]['reservations'][0]);
                $this->assertArrayHasKey('doctor', $result['data'][0]['reservations'][0]);
                $this->assertArrayHasKey('card', $result['data'][0]['reservations'][0]);
                $this->assertArrayHasKey('email', $result['data'][0]['reservations'][0]);
                $this->assertArrayHasKey('phone', $result['data'][0]['reservations'][0]);
                $this->assertArrayHasKey('sex', $result['data'][0]['reservations'][0]);
                $this->assertArrayHasKey('center_id', $result['data'][0]['reservations'][0]);
                $this->assertArrayHasKey('test_code', $result['data'][0]['reservations'][0]);
                $this->assertArrayHasKey('appointment_time', $result['data'][0]['reservations'][0]);
                $this->assertArrayHasKey('birth_date', $result['data'][0]['reservations'][0]);
                $this->assertArrayHasKey('discount_code', $result['data'][0]['reservations'][0]);
                $this->assertArrayHasKey('webOrderId', $result['data'][0]['reservations'][0]);

                if (isset($result['data'][0]['reservations'][0])) {
                    $this->assertSame($date, date('Y-m-d', strtotime($result['data'][0]['reservations'][0]['appointment_time'])));

                    $last = Arr::last($result['data'][0]['reservations']);

                    $this->assertSame($date, date('Y-m-d', strtotime($last['appointment_time'])));
                }
            }
        }
    }

    /**
     * STUBS
     */

    /**
     * @coversNothing
     * @return array
     */
    private function getReservationStub(): array
    {
        return [
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
    }

    /**
     * @coversNothing
     * @return array
     */
    private function getRequestForGettingIntervalsStub(): array
    {
        return [
            'days' => rand(0, 10),
            'startDate' => Carbon::now()->format('Y-m-d'),
            'centerId' => 1
        ];
    }

    /**
     * @coversNothing
     * @return array
     */
    private function getRequestForGettingIntervalsForCenterStub(): array
    {
        return [
            'centerId' => 26
        ];
    }

    /**
     * @coversNothing
     * @return array
     */
    private function getRequestForStoringIntervalStub(): array
    {
        return [
            'centerId' => 1,
            'testCode' => '1',
            'timeFrom' =>  Carbon::now()->format('Y-m-d H:i:s'),
            'timeTo' => Carbon::now()->format('Y-m-d H:i:s'),
            'webOrderId' => 'web_order_id_1',
            'test' => '345',
            0 => null
        ];
    }

    /**
     * @coverNothing
     * @return array
     */
    private function getCenterScheduleStub()
    {
        return [
            'centerId' => 1,
            'centerName' => 'TestName',
            'day' => [
                'start' => '09.00',
                'end' => '18:00',
            ],
            'fri' => [
                'start' => '09.00',
                'end' => '18:00',
            ],
            'sat' => [
                'start' => '09.00',
                'end' => '18:00',
            ],
            'sun' => [
                'start' => '09.00',
                'end' => '18:00',
            ]
        ];
    }

    /**
     * @coversNothing
     * @return array
     */
    private function getDayScheduleStub(): array
    {
        return [
            'from' => '09:00',
            'to' => '18.00'
        ];
    }

    /**
     * @coversNothing
     * @return array
     */
    private function getDayOffScheduleStub(): array
    {
        return [
            'from' => '00:00',
            'to' => '00.00'
        ];
    }

    /**
     * @coversNothing
     * @return array
     */
    private function getExistingReservationsStub(): array
    {
        return [
            [
                'appointment_time' => '2020-05-02 15:30:00',
            ],
            [
                'appointment_time' => '2020-05-03 15:30:00',
            ]
        ];
    }
}
