<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UserTypesSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(DepartamentosSeeder::class);
        $this->call(SemestresSeeder::class);
        $this->call(DisciplinasSeeder::class);
        $this->call(DisciplinaOfertadasSeeder::class);
        $this->call(TurmasSeeder::class);
        $this->call(TurmaTutorsSeeder::class);
    }
}
