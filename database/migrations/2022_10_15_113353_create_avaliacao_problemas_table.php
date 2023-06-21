<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAvaliacaoProblemasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('avaliacao_problemas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('problema_unidade_id');
            $table->foreign('problema_unidade_id', 'problemaUnidadesFk')->references('id')->on('problema_unidades');
            $table->unsignedBigInteger('aluno_id');
            $table->foreign('aluno_id')->references('id')->on('users');
            $table->unsignedBigInteger('barema_id');
            $table->foreign('barema_id')->references('id')->on('baremas');
            $table->text('feedback');
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
        Schema::dropIfExists('avaliacao_problemas');
    }
}
