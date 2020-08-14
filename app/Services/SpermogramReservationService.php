<?php

namespace App\Services;

use App\Jobs\GetDiscountCodeJob;
use App\Jobs\GetWebOrderIdJob;
use App\Models\Reservation;
use App\Models\UniqueTests;
use App\Services\ApiRequests\SiteRequest;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

/**
 * Class SpermogramReservationService
 * @package App\Services
 */
class SpermogramReservationService
{
    /**
     * Return structured response with intervals.
     *
     * @param Request $request
     * @return array
     */
    public function getIntervals(Request $request): array
    {
        $days = $request->get('days', 5);
        $dateFrom = $request->get('startDate', date('Y-m-d', strtotime('+1 day')));
        $centerId = $request->get('centerId');

        $existingReservations = $this->getReservedAppointmentDates($centerId, $dateFrom);

        $centerSchedule = UniqueTests::getSpermogramCenterSchedule($centerId);

        $dateTo = date('Y-m-d', strtotime("{$dateFrom} + {$days} day"));
        $period = CarbonPeriod::create($dateFrom, $dateTo);

        $dates = $this->getFreeIntervalsInDatesRange($period, $centerSchedule, $existingReservations);

        return ['days' => $dates];
    }

    /**
     * Get free intervals in range of dates for center.
     *
     * @param CarbonPeriod $period
     * @param array $centerSchedule
     * @param array $existingReservations
     * @return array
     */
    private function getFreeIntervalsInDatesRange(CarbonPeriod $period, array $centerSchedule, array $existingReservations): array
    {
        $dates = [];

        foreach ($period as $date) {
            $daySchedule = CenterService::getCenterUniqueScheduleByDay($date->dayOfWeek, $centerSchedule);
            $freeIntervals = $this->getIntervalsForTimeRange($date->format('Y-m-d'), $daySchedule, $existingReservations);

            $dates[] = [
                'date' => $date->format('Y-m-d'),
                'free' => count($freeIntervals),
                'intervals' => $freeIntervals
            ];
        }

        return $dates;
    }


    /**
     * Generate array of intervals of specified length for given range of time.
     *
     * @param string $date
     * @param array $daySchedule
     * @param array $existingReservations
     * @return array
     */
    private function getIntervalsForTimeRange(string $date, array $daySchedule, array $existingReservations): array
    {
        $scheduleFrom = (string) str_replace('.', ':', $daySchedule['from']);
        $scheduleTo = (string) str_replace('.', ':', $daySchedule['to']);

        if ($scheduleFrom === '00:00' && $scheduleTo === '00:00') {
            return [];
        }

        $intervals = [];
        $nextIntervalStart = $scheduleFrom;

        while (strtotime($nextIntervalStart) < strtotime($scheduleTo)) {
            $timeFrom = $nextIntervalStart;
            $nextIntervalStart = $timeTo = date('H:i', strtotime("{$nextIntervalStart} + 30 minutes"));

            if (array_search("{$date} {$timeFrom}:00", array_column($existingReservations, 'appointment_time')) !== false) {
                continue;
            }

            $intervals[] = [
                'timeFrom' => $timeFrom,
                'timeTo' => $nextIntervalStart
            ];
        }

        return $intervals;
    }

    /**
     * Parse data to be inserted as reservation record.
     *
     * @param Request $request
     * @return array
     */
    public function prepareReservationData(Request $request): array
    {
        return [
            'middle_name' => $request->get('middleName'),
            'first_name' => $request->get('firstName'),
            'last_name' => $request->get('lastName'),
            'doctor' => $request->get('doctor'),
            'client_card' => $request->get('card'),
            'email' => $request->get('email'),
            'phone' => preg_replace('/[^0-9]/', '', $request->get('phone')),
            'sex' => $request->get('sex'),
            'center_original_id' => $request->get('centerId'),
            'test_code' => $request->get('testCode'),
            'appointment_time' => Carbon::parse($request->get('appointmentTime'))->format('Y-m-d H:i:s'),
            'birth_date' => $request->get('birthDate')
        ];
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function storeReservation(Request $request): bool
    {
        $data = $this->prepareReservationData($request);

        $insertedId = Reservation::insertGetId($data);

        dispatch(new GetWebOrderIdJob($insertedId))->onQueue('default');
        dispatch(new GetDiscountCodeJob($insertedId))->onQueue('default');

        return (bool) $insertedId;
    }

    /**
     * Get existing reservations from reservation table.
     *
     * @param int $centerId
     * @param string $dateFrom
     * @return array
     */
    private function getReservedAppointmentDates(int $centerId, string $dateFrom): array
    {
        return Reservation::getReservationsFromDate($centerId, $dateFrom);
    }

    /**
     * @param Request $request
     * @return array
     */
    public function getReservations(Request $request): array
    {
        $days = $request->get('days', 0);
        $startDate = $request->get('startDate', date('Y-m-d', strtotime('+1 day')));
        $endDate = Carbon::parse($startDate)->addDays($days)->format('Y-m-d');

        $centerId = $request->get('centerId');

        $reservations = Reservation::getReservationsForCenterAndPeriod($centerId, $startDate, $endDate);

        return ['data' => $reservations];
    }

    /**
     * @param Request $request
     * @return array
     */
    public function getExistingReservationsGrouped(Request $request): array
    {
        $date = $request->input('date', date('Y-m-d', strtotime('+1 day')));

        $reservations = Reservation::getReservationsForDate($date);

        return $this->groupReservationsByCenters($reservations);
    }

    /**
     * Return array of reservations grouped by center in form of:
     *  [
     *      "data": [
     *          [
     *              "centerId" => 1,
     *              "reservations" => []
     *          ],
     *          [
     *              "centerId" => 26,
     *              "reservations" => []
     *          ]
     *      ]
     *  ]
     *
     * @param Collection $reservations
     * @return array
     */
    private function groupReservationsByCenters(Collection $reservations): array
    {
        $groupedReservations = [];

        foreach ($reservations as $reservation) {

            if (!in_array($reservation->center_id, array_column($groupedReservations, 'centerId'))) {
                $groupedReservations[] = [
                    'centerId' => $reservation->center_id,
                    'reservations' => [$reservation->toArray()]
                ];
                continue;
            }

            $index = array_search($reservation->center_id, array_column($groupedReservations, 'centerId'));
            $groupedReservations[$index]['reservations'][] = $reservation->toArray();

        }

        return ['data' => $groupedReservations];
    }

    /**
     * @param int $reservationId
     * @return void
     */
    public function approveReservation(int $reservationId): void
    {
        $reservation = Reservation::find($reservationId);
        $webOrderId = $this->getWebOrderId($reservation);

        if ($webOrderId === null) {
            throw new \RuntimeException('Order id not received');
        }

        $reservation->webOrderId = $webOrderId;
        $reservation->save();
    }

    /**
     * @param Reservation $reservation
     * @return string|null
     */
    private function getWebOrderId(Reservation $reservation): ?string
    {
        return (new SiteRequest())->getWebOrderIdForReservation($reservation);
    }

    /**
     * @param int $reservationId
     * @return void
     */
    public function approveClientCard(int $reservationId): void
    {
        $cardService = app(CardService::class);

        $reservation = Reservation::find($reservationId);
        $isValid = $cardService->checkCardIsValid($reservation->client_card);

        if (!$isValid) {
            throw new \RuntimeException('Client card is not validated');
        }

        $reservation->discount_code = $cardService->prepareDiscountCode($reservation->client_card);
        $reservation->save();
    }
}
