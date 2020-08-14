<?php

namespace App\Services;

/**
 * Class CenterService
 * @package App\Services
 */
class CenterService
{
    /**
     * @var array|string[]
     */
    private static array $daysOfWeek = ['sun', 'day', 'day', 'day', 'day', 'fri', 'sat'];

    /**
     * @param int $dayOfWeek [0] => sunday, [1] => monday, ..., [6] => saturday
     * @param array $centerSchedule
     * @return array
     */
    public static function getCenterUniqueScheduleByDay(int $dayOfWeek, array $centerSchedule): array
    {
        $dayTable['from'] = $centerSchedule[self::$daysOfWeek[$dayOfWeek]]['start'];
        $dayTable['to'] = $centerSchedule[self::$daysOfWeek[$dayOfWeek]]['end'];

        return $dayTable;
    }

    /**
     * @return array|object[]
     */
    public function getCentersForSpermogram(): array
    {
         return [
             (object) [
                'test' => [
                    'code' => '',
                    'nameUa' => ''
                ],
                'centers' => [
                    [
                        'id' => 0,
                        'nameUa' => ''
                    ]
                ]
             ],
             (object) [
                'test' => [
                    'code' => '',
                    'nameUa' => ''
                ],
                'centers' => [
                    [
                        'id' => 0,
                        'nameUa' => ''
                    ],
                ]
            ],
        ];
    }
}
