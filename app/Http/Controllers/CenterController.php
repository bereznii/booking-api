<?php

namespace App\Http\Controllers;

use App\Services\CenterService;
use App\Services\LogService;
use Illuminate\Http\JsonResponse;

/**
 * Class CenterController
 * @package App\Http\Controllers
 */
class CenterController extends Controller
{
    /**
     * @var LogService
     */
    private LogService $logService;

    /**
     * @var CenterService
     */
    private CenterService $centerService;

    /**
     * CardController constructor.
     * @param LogService $logService
     * @param CenterService $centerService
     */
    public function __construct(LogService $logService, CenterService $centerService)
    {
        $this->logService = $logService;
        $this->centerService = $centerService;
    }

    /**
     * @return JsonResponse
     */
    public function getSpermogramCenters(): JsonResponse
    {
        $this->logService->log(__METHOD__, 'Request for centers');

        $centers = $this->centerService->getCentersForSpermogram();

        return response()->json($centers, 200);
    }
}
