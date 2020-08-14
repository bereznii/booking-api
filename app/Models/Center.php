<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Reservation
 * @package App\Models
 */
class Center extends Model
{
    /**
     * @var string
     */
    protected $table = 'centers';

    /**
     * @param int $centerId
     * @return array
     */
    public static function getCenterSchedule(int $centerId): array
    {
        $center = self::where('id', $centerId)
            ->select(['day_t_table','fri_t_table','sat_t_table','sun_t_table'])
            ->first();

        return isset($center)
            ? $center->toArray()
            : [];
    }
}
