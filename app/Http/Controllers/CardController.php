<?php

namespace App\Http\Controllers;

use App\Http\Validation\CardValidation;
use App\Services\CardService;
use App\Services\LogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class CardController
 * @package App\Http\Controllers
 */
class CardController extends Controller
{
    use CardValidation;

    /**
     * @var CardService
     */
    private CardService $cardService;

    /**
     * @var LogService
     */
    private LogService $logService;

    /**
     * CardController constructor.
     * @param CardService $cardService
     * @param LogService $logService
     */
    public function __construct(CardService $cardService, LogService $logService)
    {
        $this->cardService = $cardService;
        $this->logService = $logService;
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function checkCard(Request $request): JsonResponse
    {
        $this->logService->log(__METHOD__, 'Request for checking employee card code', $request->all());

        $this->validate(...$this->checkCardRequest($request));

        $response = $this->cardService->checkCard($request);

        return response()->json($response, 200);
    }
}
