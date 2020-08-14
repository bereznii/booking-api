<?php

namespace App\Console\Commands;

use App\Models\Center;
use App\Models\UniqueTests;
use App\Services\LogService;
use Illuminate\Console\Command;

/**
 * Class UpdateUniqueTestsTableCommand
 * @package App\Console\Commands
 */
class UpdateUniqueTestsTableCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'updateTable:unique_tests';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Update unique_tests table.";

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $uniqueTestsRecords = UniqueTests::all();

        foreach ($uniqueTestsRecords as $record) {

            $centersIds = array_column($record->centers, 'centerId');
            $newCentersSchedules = Center::whereIn('original_id', $centersIds)
                ->select(['original_id','name_ua','day_t_table','fri_t_table','sat_t_table','sun_t_table'])
                ->get();

            $newCentersData = [];
            foreach ($record->centers as $key => $center) {
                $newSchedule = $newCentersSchedules->where('original_id', $center['centerId'])->first();
                $newCentersData[$key] = $this->formatNewCenterData($newSchedule, $center);
            }

            $record->update(['centers' => json_encode($newCentersData, JSON_UNESCAPED_UNICODE)]);

        }

        LogService::instantiate()
            ->setContext(__METHOD__)
            ->setMessage('Table unique_tests updated')
            ->store();

        $this->info("Table unique_tests updated successfully.");
    }

    /**
     * @param Center $newSchedule
     * @param array $center
     * @return array
     */
    private function formatNewCenterData(Center $newSchedule, array $center): array
    {
        $data['centerId'] = $center['centerId'];
        $data['centerName'] = $newSchedule->name_ua;

        $daySchedule = json_decode($newSchedule->day_t_table, true);
        $friSchedule = json_decode($newSchedule->fri_t_table, true);
        $satSchedule = json_decode($newSchedule->sat_t_table, true);
        $sunSchedule = json_decode($newSchedule->sun_t_table, true);

        if ((int) $data['centerId'] === 1) {
            $endingTime = 'serv_finish';
        } else {
            $endingTime = 'curier';
        }

        $data['day']['start'] = $daySchedule['start'];
        $data['day']['end'] = $daySchedule[$endingTime];
        $data['fri']['start'] = $friSchedule['start'];
        $data['fri']['end'] = $friSchedule[$endingTime];
        $data['sat']['start'] = $satSchedule['start'];
        $data['sat']['end'] = $satSchedule[$endingTime];
        $data['sun']['start'] = $sunSchedule['start'];
        $data['sun']['end'] = $sunSchedule[$endingTime];

        return $data;
    }
}
