<?php

namespace App\Http\Controllers;

use App\Http\Validation\ReservationValidation;
use App\Services\LogService;
use App\Services\SpermogramReservationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * Class ReservationController
 * @package App\Http\Controllers
 */
class ReservationController extends Controller
{
    use ReservationValidation;

    /**
     * @var SpermogramReservationService
     */
    private SpermogramReservationService $reservationService;

    /**
     * @var LogService
     */
    private LogService $logService;

    /**
     * @param SpermogramReservationService $reservationService
     * @param LogService $logService
     */
    public function __construct(SpermogramReservationService $reservationService, LogService $logService)
    {
        $this->reservationService = $reservationService;
        $this->logService = $logService;
    }

    /**
     * Get free time intervals for given range of time and center.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function getFreeIntervals(Request $request): JsonResponse
    {
        $this->logService->log(__METHOD__, 'Request for free intervals', $request->all());

        $this->validate(...$this->getFreeIntervalsRequest($request));

        $intervals = $this->reservationService->getIntervals($request);

        return response()->json($intervals, 200);
    }

    /**
     * Store reservation for interval.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function storeInterval(Request $request): JsonResponse
    {
        $this->logService->log(__METHOD__, 'Request for storing reservation', $request->all());

        $this->validate(...$this->storeIntervalRequest($request));

        $result = $this->reservationService->storeReservation($request);

        return $result
            ? response()->json(['success' => true], 201)
            : response()->json(['success' => false], 500);
    }

    /**
     * Get list of existing reservation for given center and time period.
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function getExistingReservations(Request $request): JsonResponse
    {
        $this->logService->log(__METHOD__, 'Request for stored reservation', $request->all());

        $this->validate(...$this->getExistingReservationsRequest($request));

        $reservations = $this->reservationService->getReservations($request);

        return response()->json($reservations,200);
    }

    /**
     * Get grouped list of existing reservation for next day (or given date).
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function getExistingReservationsGrouped(Request $request): JsonResponse
    {
        $this->logService->log(__METHOD__, 'Request for existing reservation grouped', $request->all());

        $this->validate(...$this->getExistingReservationsGroupedRequest($request));

        $reservations = $this->reservationService->getExistingReservationsGrouped($request);

        return response()->json($reservations, 200);
    }
}
