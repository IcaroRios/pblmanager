<?php

use App\Models\Disciplina;
use Illuminate\Database\Seeder;

class DisciplinasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Disciplina::create([
            'code' => 'EXA857',
            'name' => 'ENGENHARIA DE SOFTWARE',
            'workload' => 60,
            'departamento_id' => 1,
            'folder_id' => 'disciplinas/EXA857 - ENGENHARIA DE SOFTWARE'
        ]);
          Disciplina::create([
              'code' => 'TEC498',
              'name' => 'PROJETO DE CIRCUITOS DIGITAIS',
              'workload' => 60,
              'departamento_id' => 2,
              'folder_id' => 'disciplinas/TEC498 - PROJETO DE CIRCUITOS DIGITAIS'
          ]);
          Disciplina::create([
              'code' => 'EXA863',
              'name' => 'PROGRAMAÇÃO I',
              'workload' => 60,
              'departamento_id' => 1,
              'folder_id' => 'disciplinas/EXA863 - PROGRAMAÇÃO I'
          ]);
          Disciplina::create([
              'code' => 'TEC499',
              'name' => 'SISTEMAS DIGITAIS',
              'workload' => 60,
              'departamento_id' => 2,
              'folder_id' => 'disciplinas/TEC499 - SISTEMAS DIGITAIS'
          ]);
    }
}
