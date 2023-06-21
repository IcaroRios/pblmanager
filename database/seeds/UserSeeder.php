<?php

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        User::create([
            'username' => "adm@adm",
            'password' => "adm12345",
            'email' => "ambientewebpbl@gmail.com",
            'enrollment' => "00000000",
            'user_type' => 1,
            'first_name' => "Administrador",
            'surname' => "PBL"
        ]);

        User::create([
            'username' => "roberto@tutor",
            'password' => "roberto12345",
            'email' => "romaiajr5@gmail.com",
            'enrollment' => "18111240",
            'user_type' => 2,
            'first_name' => "Roberto",
            'surname' => "Maia"
        ]);

        User::create([
            'username' => "claudia@tutor",
            'password' => "claudia12345",
            'email' => "claudia@gmail.com",
            'enrollment' => "00000001",
            'user_type' => 2,
            'first_name' => "Claudia",
            'surname' => "Pinto"
        ]);

        User::create([
            'username' => "icaro@aluno",
            'password' => "icaro123",
            'email' => "icaro@gmail.com",
            'enrollment' => "00000001",
            'user_type' => 4,
            'first_name' => "icaro",
            'surname' => "rios"
        ]);
    }
}
