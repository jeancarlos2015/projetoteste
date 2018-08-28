<?php

namespace App\Http\Controllers;

use App\Http\Models\ModeloDeclarativo;
use App\Http\Models\Projeto;
use App\http\Models\Repositorio;
use App\Http\Repositorys\ModeloDeclarativoRepository;
use App\Http\Repositorys\ObjetoFluxoRepository;
use App\Http\Repositorys\RegraRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PainelModeloDeclarativoController extends Controller
{
    private function rotas()
    {
        if (Auth::user()->email === 'jeancarlospenas25@gmail.com') {
            return [
                'controle_objeto_fluxo_index',
                'controle_regras_index'
            ];
        } else if (!empty(Auth::user()->repositorio)) {
            return [
                'controle_objeto_fluxo_index',
                'controle_regras_index'
            ];
        }
        return [];

    }

    private function titulos()
    {
        if (Auth::user()->email === 'jeancarlospenas25@gmail.com') {
            return [

                'Objetos de Fluxos',
                'Regras'
            ];
        } else if (!empty(Auth::user()->repositorio)) {
            return [

                'Objetos de Fluxos',
                'Regras'
            ];
        }
        return [];
    }

    private function quantidades($codmodelodeclarativo)
    {
        $qt_objetos_fluxos = ObjetoFluxoRepository::listar_por_modelo_declarativo($codmodelodeclarativo)->count();
        $qt_regras = RegraRepository::listar()->count();
        if (Auth::user()->email === 'jeancarlospenas25@gmail.com') {

            return [

                $qt_objetos_fluxos,
                $qt_regras,
            ];
        } else if (!empty(Auth::user()->repositorio)) {
            return [
                $qt_objetos_fluxos,
                $qt_regras,
            ];
        }
        return 0;
    }

    public function create($codrepositorio, $codprojeto)
    {
        $titulos = ModeloDeclarativo::titulos();
        $dados = ModeloDeclarativo::dados();
        $tipo = 'modelo_declarativo';
        $repositorio = Repositorio::findOrFail($codrepositorio);
        $projeto = Projeto::findOrFail($codprojeto);
        return view('controle_modelos_declarativos.modelos_declarativos.create',
            compact('titulos', 'dados', 'tipo', 'repositorio', 'projeto'));
    }


    public
    function store(Request $request)
    {
        $codprojeto = $request->codprojeto;
        $codrepositorio = $request->codrepositorio;
        $data['all'] = $request->all();
        $data['validacao'] = ModeloDeclarativo::validacao();
        if (!$this->exists_errors($data)) {
            $request->request->add(['codusuario' => Auth::user()->codusuario]);
            if (!ModeloDeclarativoRepository::existe($request->nome)) {
                $modelo = ModeloDeclarativoRepository::incluir($request);
                return redirect()->route('controle_objeto_fluxo_index',
                    [
                        'codmodelodeclarativo' => $modelo->codmodelodeclarativo
                    ]);
            }else{
                $titulos = ModeloDeclarativo::titulos();
                $dados = ModeloDeclarativo::dados();
                $tipo = 'modelo_declarativo';
                $modelo = ModeloDeclarativoRepository::listar()->where('nome', $request->nome)->first();
                $repositorio = $modelo->repositorio;
                $projeto = $modelo->projeto;
                return view('controle_modelos_declarativos.modelos_declarativos.create',
                    compact('titulos', 'dados', 'tipo', 'repositorio', 'projeto','modelo'));
            }


        }

        $erros = $this->get_errors($data);
        return redirect()->route('controle_modelos_declarativos_create', [
            'codrepositorio' => $codrepositorio,
            'codprojeto' => $codprojeto
        ])
            ->withErrors($erros)
            ->withInput();
    }

    public function painel_modelo_declarativo($codmodelodeclarativo){
        $tipo = 'modelos';
        $titulos = $this->titulos();
        $rotas = $this->rotas();
        $quantidades = $this->quantidades($codmodelodeclarativo);
        $modelodeclarativo = ModeloDeclarativo::findOrFail($codmodelodeclarativo);
        if (count($rotas) == 0) {
            $data['mensagem'] = "Favor solicitar ao administrador que vincule sua conta a uma repositório!!";
            $data['tipo'] = 'success';
            $this->create_log($data);
        }

        return view('controle_modelos_declarativos.modelos_declarativos.show', compact('titulos', 'quantidades', 'rotas', 'tipo','modelodeclarativo'));
    }
}