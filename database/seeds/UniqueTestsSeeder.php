<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UniqueTestsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('unique_tests')->truncate();

        DB::table('unique_tests')->insert([
            'id' => 1,
            'name' => 'Спермограма і МАР-тест',
            'codes' => '["4015","4039","4161"]',
            'centers' => '[{"centerId":"26","centerName":"Київ, проспект Палладіна 46\/2","day":{"start":"07.00","end":"14.00"},"fri":{"start":"07.00","end":"14.00"},"sat":{"start":"07.30","end":"12.00"},"sun":{"start":"08.00","end":"12.00"}},{"centerId":"107","centerName":"Дніпро, вулиця Гоголя 15","day":{"start":"07.00","end":"14.00"},"fri":{"start":"07.00","end":"14.00"},"sat":{"start":"07.30","end":"12.00"},"sun":{"start":"08.00","end":"12.00"}},{"centerId":"130","centerName":"Одеса, вулиця Сегедська 14-А","day":{"start":"07.00","end":"13.30"},"fri":{"start":"07.00","end":"13.00"},"sat":{"start":"07.30","end":"11.30"},"sun":{"start":"08.00","end":"11.30"}},{"centerId":"1","centerName":"Харків, вулиця Лермонтовська 27","day":{"start":"09.00","end":"14.00"},"fri":{"start":"09.00","end":"14.00"},"sat":{"start":"09.00","end":"14.00"},"sun":{"start":"00:00","end":"00:00"}},{"centerId":"65","centerName":"Львів, вулиця Коновальця 33","day":{"start":"07.00","end":"14.00"},"fri":{"start":"07.00","end":"14.00"},"sat":{"start":"07.30","end":"13.00"},"sun":{"start":"08.00","end":"13.00"}},{"centerId":"110","centerName":"Чернівці, вулиця Авангардна 53","day":{"start":"07.00","end":"14.00"},"fri":{"start":"07.00","end":"14.00"},"sat":{"start":"07.30","end":"13.00"},"sun":{"start":"08.00","end":"13.00"}},{"centerId":"8","centerName":"Вінниця, вулиця Пирогова 37","day":{"start":"07.00","end":"13.30"},"fri":{"start":"07.00","end":"13.30"},"sat":{"start":"07.30","end":"13.30"},"sun":{"start":"08.00","end":"13.30"}}]',
        ]);
    }
}
