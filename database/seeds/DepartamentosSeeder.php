<?php

use App\Models\Departamento;
use Illuminate\Database\Seeder;

class DepartamentosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Departamento::create([
            'name' => "Departamento de Ciências Exatas",
            'abbreviation' => "DEXA"
        ]);

        Departamento::create([
            'name' => "Departamento de Tecnologia",
            'abbreviation' => "DTEC"
        ]);
    }
}
