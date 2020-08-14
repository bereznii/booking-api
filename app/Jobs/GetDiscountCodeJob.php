<?php

namespace App\Jobs;

use App\Services\LogService;
use App\Services\SpermogramReservationService;
use Throwable;

class GetDiscountCodeJob extends Job
{
    /**
     * @var SpermogramReservationService
     */
    private SpermogramReservationService $spermogramReservationService;

    /**
     * @var LogService
     */
    private LogService $logService;

    /**
     * @var int
     */
    private int $reservationId;

    /**
     * Create a new job instance.
     *
     * @param int $reservationId
     */
    public function __construct(int $reservationId)
    {
        $this->logService = new LogService();
        $this->spermogramReservationService = new SpermogramReservationService();
        $this->reservationId = $reservationId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->spermogramReservationService->approveClientCard($this->reservationId);

        (new LogService())->log(__METHOD__, 'Getting discount code for client card succeed', [
            'reservationId' => $this->reservationId
        ]);
    }

    /**
     * The job failed to process.
     *
     * @param Throwable $exception
     * @return void
     */
    public function failed(Throwable $exception)
    {
        (new LogService('ERROR'))->log(__METHOD__, 'Getting discount code for client card failed', [
            'reservationId' => $this->reservationId
        ]);
    }
}
