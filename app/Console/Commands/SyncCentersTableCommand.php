<?php

namespace App\Console\Commands;

use App\Models\Center;
use App\Services\ApiRequests\SiteRequest;
use App\Services\LogService;
use Illuminate\Console\Command;

/**
 * Class SyncCentersTableCommand
 * @package App\Console\Commands
 */
class SyncCentersTableCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'syncTable:centers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Synchronize data in centers table with main db.";

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $centers = $this->getExternalCentersList() ?? [];

        if (empty($centers)) {
            (new LogService('ERROR'))->log(__METHOD__, 'Table centers not updated');
            return 0;
        }

        foreach ($centers['data'] as $center) {

            $centerId = $center['center_id'];

            $dataToStore = $this->formatData($center);

            $centerExists = Center::where('original_id', $centerId)->exists();
            if ($centerExists) {
                Center::where('original_id', $centerId)->update($dataToStore);
            } else {
                Center::insert($dataToStore);
            }

        }

        LogService::instantiate()
            ->setContext(__METHOD__)
            ->setMessage('Table centers updated')
            ->store();

        $this->info("Table centers was synchronized successfully.");

        return 0;
    }

    /**
     * Make array with needed centers data.
     *
     * @param array $center
     * @return array
     */
    private function formatData(array $center): array
    {
        $data['name_ua'] = "{$center['name_ua']}, {$center['street_type_ua']} {$center['street_ua']} {$center['house']}";
        $data['name_ru'] = "{$center['name_ru']}, {$center['street_type_ru']} {$center['street_ru']} {$center['house']}";
        $data['name_en'] = "{$center['name_en']}, {$center['street_type_en']} {$center['street_en']} {$center['house']}";

        $data['day_t_table'] = $center['day_t_table'];
        $data['fri_t_table'] = $center['fri_t_table'];
        $data['sat_t_table'] = $center['sat_t_table'];
        $data['sun_t_table'] = $center['sun_t_table'];

        $data['original_id'] = $center['center_id'];
        $data['location_id'] = $center['location_id'];

        return $data;
    }

    /**
     * @return array|null
     */
    public function getExternalCentersList(): ?array
    {
        return (new SiteRequest())->getCentersList();
    }
}
