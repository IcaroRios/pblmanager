<?php

use App\Models\Semestre;
use Illuminate\Database\Seeder;

class SemestresSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Semestre::create([
            'code' => "2019.1",
            'start_date' => "2019-03-01",
            'end_date' => "2019-08-30",
        ]);

        Semestre::create([
            'code' => "2019.2",
            'start_date' => "2019-09-30",
            'end_date' => "2019-12-23",
        ]);

        Semestre::create([
            'code' => "2022.1",
            'start_date' => "2022-03-07",
            'end_date' => "2022-08-30",
        ]);
    }
}
