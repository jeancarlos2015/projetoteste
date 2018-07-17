<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Auth::routes();
Route::get('/', function () {
    return view('inicio');
})->name('/');

Route::get('/logout', function () {
    return view('inicio');
})->name('logout');

Route::prefix('admin')->middleware(['auth'])->group(
    function () {

        Route::resource('controle_organizacoes', 'OrganizacaoController');
        Route::resource('controle_tarefas', 'TarefaController');
        Route::resource('controle_projetos', 'ProjetoController');
        Route::resource('controle_modelos', 'ModeloController');
        Route::resource('controle_regras', 'RegraController');



        Route::get('controle_projetos_index/{codorganizacao}', 'ProjetoController@index')->name('controle_projetos_index');
        Route::get('show_tarefas/{codmodelo}', 'ModeloController@show_tarefas')->name('show_tarefas');
        Route::get('show_regras/{codmodelo}', 'ModeloController@show_regras')->name('show_regras');

        Route::get('todos_modelos', 'ModeloController@todos_modelos')->name('todos_modelos');
        Route::get('todos_projetos', 'ProjetoController@todos_projetos')->name('todos_projetos');
        Route::get('todas_tarefas', 'TarefaController@todas_tarefas')->name('todas_tarefas');
        Route::get('todas_regras', 'RegraController@todas_regras')->name('todas_regras');




        Route::get('controle_projetos_create/{codorganizacao}', 'ProjetoController@create')->name('controle_projetos_create');
        Route::get('controle_modelos_create/{codorganizacao}/{codprojeto}', 'ModeloController@create')->name('controle_modelos_create');
        Route::get('controle_modelos_index/{codorganizacao}/{codprojeto}', 'ModeloController@index')->name('controle_modelos_index');
        Route::get('controle_tarefas_index/{codorganizacao}/{codprojeto}/{codmodelo}', 'TarefaController@index')->name('controle_tarefas_index');
        Route::get('controle_tarefas_create/{codorganizacao}/{codprojeto}/{codmodelo}/{codregra}', 'TarefaController@create')->name('controle_tarefas_create');
        Route::get('controle_regras_create/{codorganizacao}/{codprojeto}/{codmodelo}', 'RegraController@create')->name('controle_regras_create');
        Route::get('controle_regras_index/{codorganizacao}/{codprojeto}/{codmodelo}', 'RegraController@index')->name('controle_regras_index');
        Route::post('escolhe_modelo', 'ModeloController@escolhe_modelo')->name('escolhe_modelo');


        //Versionamento
        Route::resource('controle_versao', 'GitController');
        Route::get('init', 'GitController@init')->name('init');
        Route::post('create', 'GitController@create')->name('create');
        Route::post('commit', 'GitController@commit')->name('commit');
        Route::post('merge_checkout', 'GitController@merge_checkout')->name('merge_checkout');
        Route::post('delete', 'GitController@delete')->name('delete');
        Route::get('index_reset_files', 'GitController@index_reset_files')->name('index_reset_files');

        Route::get('index_init', 'GitController@index_init')->name('index_init');
        Route::get('index_painel', 'GitController@index')->name('index_painel');
        Route::get('index_merge_checkout', 'GitController@index_merge_checkout')->name('index_merge_checkout');
        Route::get('index_create_delete', 'GitController@index_create_delete')->name('index_create_delete');
        Route::get('index_commit_branch', 'GitController@index_commit_branch')->name('index_commit_branch');
        Route::get('index_pull_push', 'GitController@index_pull_push')->name('index_pull_push');





        Route::get('pagina_inicializacao_repositorio', 'GitController@pagina_inicializacao_repositorio')->name('pagina_inicializacao_repositorio');

        Route::get('painel', 'OrganizacaoController@painel')->name('painel');

    });

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
