<?php

use App\Models\UserType;
use Illuminate\Database\Seeder;

class UserTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        UserType::create(['type' => "administrador"]);
        UserType::create(['type' => "tutor"]);
        UserType::create(['type' => "coordenador"]);
        UserType::create(['type' => "aluno"]);
    }
}
