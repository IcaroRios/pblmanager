<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterBaremaTableAddProblemaIdField extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('baremas', function (Blueprint $table) {
            $table->unsignedBigInteger('problema_id');
            $table->foreign('problema_id')->references('id')->on('problemas');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('baremas', function (Blueprint $table) {
            $table->dropForeign('problema_id');
            $table->dropColumn('problema_id');
        });
    }
}
