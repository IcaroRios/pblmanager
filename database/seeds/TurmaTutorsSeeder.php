<?php

use App\Models\TurmaTutor;
use Illuminate\Database\Seeder;

class TurmaTutorsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TurmaTutor::create([
            'user_id' =>  2,
            'turma_id' =>  1
        ]);
        TurmaTutor::create([

            'user_id' =>  3,
            'turma_id' =>  2
        ]);
        TurmaTutor::create([

            'user_id' =>  2,
            'turma_id' =>  3
        ]);
        TurmaTutor::create([

            'user_id' => 3,
            'turma_id' =>  4
        ]);
    }
}
