<?php

use App\Models\DisciplinaOfertada;
use Illuminate\Database\Seeder;

class DisciplinaOfertadasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DisciplinaOfertada::create([
            'disciplina_id' => 1,
            'semestre_id' => 3,
            'number_of_classes' => 2,
            'folder_id' => "disciplinas/EXA857 - ENGENHARIA DE SOFTWARE/2022.1"
        ]);
        DisciplinaOfertada::create([
            'disciplina_id' => 3,
            'semestre_id' => 3,
            'number_of_classes' => 2,
            'folder_id' => "disciplinas/EXA863 - PROGRAMAÇÃO I/2022.1"
        ]);
    }
}
