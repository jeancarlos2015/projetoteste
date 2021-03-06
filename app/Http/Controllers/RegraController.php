<?php

namespace App\Http\Controllers;

use App\Http\Models\RepresentacaoDeclarativa;
use App\http\Models\Regra;
use App\Http\Repositorys\RegraRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegraController extends ControllerAbstrata
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($codmodelodeclarativo)
    {
        $regras = RegraRepository::listar();
        $tipo = 'regra';
        $titulos = Regra::titulos();
        $modelo_declarativo = RepresentacaoDeclarativa::findOrFail($codmodelodeclarativo);
        $repositorio = $modelo_declarativo->repositorio;
        $projeto = $modelo_declarativo->projeto;
        return view('controle_regras.index', compact('regras', 'tipo', 'titulos','modelo_declarativo','repositorio','projeto'));
    }

    public function all()
    {
       $regras = RegraRepository::listar();
        $tipo = 'regra';
        $titulos = Regra::titulos();
        if (!empty(Auth::user()->repositorio)){
            $repositorio = Auth::user()->repositorio;
        }
        return view('controle_regras.all', compact('regras', 'tipo', 'titulos','repositorio'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\http\Models\Regra $regra
     * @return \Illuminate\Http\Response
     */
    public function show($codregra)
    {
        $regra = Regra::findOrFail($codregra);
        $projeto = $regra->projeto;
        $repositorio = $regra->repositorio;
        return view('controle_regras.show',compact('regra','projeto','repositorio'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\http\Models\Regra $regra
     * @return \Illuminate\Http\Response
     */
    public function edit($codregra)
    {
//        dd(null);
        $regra = Regra::findOrFail($codregra);
        $titulos = Regra::titulos();
        $dados = Regra::dados();
        $dados[0]->valor = $regra->nome;
        return view('controle_regras.edit',compact('regra','titulos','dados'));
//        echo 'Página em construção';
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\http\Models\Regra $regra
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $codregra)
    {

        try{
            $regra = RegraRepository::atualizar($request, $codregra);
            $data['tipo'] = 'success';
            $this->create_log($data);
        }catch (\Exception $ex) {
            $data['mensagem'] = $ex->getMessage();
            $data['tipo'] = 'error';
            $data['pagina'] = 'Painel';
            $data['acao'] = 'merge_checkout';
            $this->create_log($data);
        }
        return redirect()->route('controle_regras_index',[$regra->cod_modelo_declarativo]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\http\Models\Regra $regra
     * @return \Illuminate\Http\Response
     */
    public function destroy($codregra)
    {
        try{
            $regra = RegraRepository::excluir($codregra);
            $data['tipo'] = 'success';
            $this->create_log($data);
        }catch (\Exception $ex) {
            $data['mensagem'] = $ex->getMessage();
            $data['tipo'] = 'error';
            $data['pagina'] = 'Painel';
            $data['acao'] = 'merge_checkout';
            $this->create_log($data);
        }
        return redirect()->route('controle_regras_index',[$regra->cod_modelo_declarativo]);

    }
}
