<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProblemaUnidadesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('problema_unidades', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('disciplina_ofertada_id');
            $table->foreign('disciplina_ofertada_id')->references('id')->on('disciplina_ofertadas');
            $table->unsignedBigInteger('problema_id');
            $table->foreign('problema_id')->references('id')->on('problemas');
            $table->date('data_entrega');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('problema_unidades');
    }
}
