<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentacoesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documentacoes', function (Blueprint $table) {
            $table->increments('coddocumentacao');
            $table->bigInteger('codusuario')->unsigned();
            $table->boolean('visibilidade');
            $table->string('nome');
            $table->string('descricao');
            $table->string('link');
            $table->timestamps();
        });

        Schema::table('documentacoes', function($table) {
            $table->foreign('codusuario')->references('codusuario')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('documentacoes');
    }
}
