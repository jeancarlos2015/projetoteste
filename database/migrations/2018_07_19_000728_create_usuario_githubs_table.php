<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsuarioGithubsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usuarios_github', function (Blueprint $table) {
            $table->bigIncrements('codusuariogithub');
            $table->string('usuario_github',4000);
            $table->string('email_github');
            $table->string('repositorio_atual')->nullable();
            $table->string('branch_atual')->nullable();
            $table->string('senha_github',4000);
            $table->bigInteger('codusuario')->unsigned();

//            $table->foreign('codusuario')->references('codusuario')->on('users');
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
        Schema::dropIfExists('usuario_githubs');
    }
}
