<?php

use App\Models\Turma;
use Illuminate\Database\Seeder;

class TurmasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Turma::create([
            'code' => 'P01',
            'disciplina_ofertada_id' => 1,
            'class_days' => "seg,qua",
            'class_time' => "15:30 - 17:30",
            'folder_id' => 'disciplinas/EXA857 - ENGENHARIA DE SOFTWARE/2022.1/P01'
        ]);
        Turma::create([
            'code' => 'P02',
            'disciplina_ofertada_id' => 1,
            'class_days' => "seg,qua",
            'class_time' => "15:30 - 17:30",
            'folder_id' => 'disciplinas/EXA857 - ENGENHARIA DE SOFTWARE/2022.1/P02'
        ]);
        Turma::create([
            'code' => 'P01',
            'disciplina_ofertada_id' => 2,
            'class_days' => "ter,qui",
            'class_time' => "13:30 - 15:30",
            'folder_id' => 'disciplinas/EXA863 - PROGRAMAÇÃO I/2022.1/P01'
        ]);
        Turma::create([
            'code' => 'P02',
            'disciplina_ofertada_id' => 2,
            'class_days' => "ter,qui",
            'class_time' => "13:30 - 15:30",
            'folder_id' => 'disciplinas/EXA863 - PROGRAMAÇÃO I/2022.1/P02'
        ]);
    }
}
