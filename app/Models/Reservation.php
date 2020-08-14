<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Reservation
 * @package App\Models
 */
class Reservation extends Model
{
    /**
     * @var string
     */
    protected $table = 'reservations';

    /**
     * @var string[]
     */
    private static array $fieldsForReport = [
        'first_name', 'last_name', 'middle_name', 'doctor', 'client_card as card',
        'email', 'phone', 'birth_date', 'sex', 'center_original_id as center_id',
        'test_code', 'appointment_time', 'discount_code', 'webOrderId'
    ];

    /**
     * @param int $centerId
     * @param string $dateFrom
     * @return array
     */
    public static function getReservationsFromDate(int $centerId, string $dateFrom): array
    {
        return self::select(['appointment_time'])
            ->where(function($query) {
                $query->whereIn('test_code', ['']);
            })
            ->where('center_original_id', $centerId)
            ->where('appointment_time', '>=', $dateFrom)
            ->get()
            ->toArray();
    }

    /**
     * @param int $centerId
     * @param string $startDate
     * @param string $endDate
     * @return array
     */
    public static function getReservationsForCenterAndPeriod(int $centerId, string $startDate, string $endDate): array
    {
        return self::select(self::$fieldsForReport)
            ->where('center_original_id', $centerId)
            ->whereDate('appointment_time', '>=', $startDate)
            ->whereDate('appointment_time', '<=', $endDate)
            ->orderBy('appointment_time')
            ->get()
            ->toArray();
    }

    /**
     * @param string $date
     * @return Collection
     */
    public static function getReservationsForDate(string $date): Collection
    {
        return self::select(self::$fieldsForReport)
            ->whereDate('appointment_time', $date)
            ->orderBy('appointment_time')
            ->get();
    }
}
