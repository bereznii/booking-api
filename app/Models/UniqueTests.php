<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Class UniqueTests
 * @package App\Models
 */
class UniqueTests extends Model
{
    /**
     * @var string
     */
    protected $table = 'unique_tests';

    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @var array
     */
    public $fillable = [
        'centers'
    ];

    /**
     * @return array
     */
    public static function getSpermogramCenters(): array
    {
        $unique = self::where('codes', 'like', '%["",%')
            ->select('centers')
            ->first();

        return isset($unique)
            ? $unique->centers
            : [];
    }

    /**
     * @return array
     */
    public static function getSpermogramTestCodes(): array
    {
        $unique = self::where('codes', 'like', '%[""%')
            ->select('codes')
            ->first();

        return isset($unique)
            ? $unique->codes
            : [];
    }

    /**
     * Get unique centers ids from cache. Unique centers ids stored for 12 hours.
     *
     * @return array
     */
    public static function getUniqueSpermogramCentersIds(): array
    {
        $cacheKey = 'unique-centers-ids';

        if (!Cache::has($cacheKey)) {
            $centers = self::getSpermogramCenters();
            $ids = array_column($centers, 'centerId');

            Cache::put($cacheKey, $ids, 43200); //12 hours

            return $ids;
        }

        return Cache::get($cacheKey, []);
    }

    /**
     * Get unique test codes from cache. Unique test codes stored for 12 hours.
     *
     * @return array
     */
    public static function getUniqueSpermogramTestsCodes(): array
    {
        $cacheKey = 'unique-tests-codes';

        if (!Cache::has($cacheKey)) {
            $codes = self::getSpermogramTestCodes();

            Cache::put($cacheKey, $codes, 43200); //12 hours

            return $codes;
        }

        return Cache::get($cacheKey, []);
    }

    /**
     * Get spermogram center schedule by center id.
     *
     * @param int $centerId
     * @return array
     */
    public static function getSpermogramCenterSchedule(int $centerId): array
    {
        $centers = self::getSpermogramCenters();

        $key = array_search($centerId, array_column($centers, 'centerId'));

        return $key !== false
            ? $centers[$key]
            : [];
    }

    /**
     * Accessor for centers.
     *
     * @param string $value
     * @return array
     */
    public function getCentersAttribute($value): array
    {
        return json_decode($value, true) ?? [];
    }

    /**
     * Accessor for codes.
     *
     * @param string $value
     * @return array
     */
    public function getCodesAttribute($value): array
    {
        return json_decode($value, true) ?? [];
    }
}
