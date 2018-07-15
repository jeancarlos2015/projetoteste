<?php

namespace App\Http\Controllers;

use App\Http\Models\Modelo;
use App\Http\Models\Organizacao;
use App\Http\Models\Projeto;
use App\Http\Models\Regra;
use App\Http\Models\Tarefa;
use App\Http\Repositorys\LogRepository;
use App\Http\Repositorys\RegraRepository;
use App\Http\Repositorys\TarefaRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegraController extends Controller
{
    public function index($codorganizacao, $codprojeto, $codmodelo)
    {
        $dado['codorganizacao'] = $codorganizacao;
        $dado['codprojeto'] = $codprojeto;
        $dado['codmodelo'] = $codmodelo;

        $regras = RegraRepository::listar_regras_por_modelo($dado);
        $titulos = Regra::titulos();
        $organizacao = Organizacao::findOrFail($codorganizacao);
        $projeto = Projeto::findOrFail($codprojeto);
        $modelo = Modelo::findOrFail($codmodelo);
        $tipo = 'regra';
        $log = LogRepository::log();

        return view('controle_regras.index', compact('titulos', 'organizacao', 'projeto', 'modelo', 'log', 'regras', 'tipo'));
    }

    public function todas_regras()
    {
        $regras = RegraRepository::listar();
        $titulos = Regra::titulos();
        $tarefas = null;
        $tipo = 'regra';
        $log = LogRepository::log(2);
        return view('controle_regras.all', compact('regras', 'titulos', 'tarefas', 'tipo', 'log'));
    }

    public function create($codorganizacao, $codprojeto, $codmodelo)
    {
        $organizacao = Organizacao::findOrFail($codorganizacao);
        $projeto = Projeto::findOrFail($codprojeto);
        $modelo = Modelo::findOrFail($codmodelo);
        return view('controle_regras.form_regra', compact('organizacao', 'projeto', 'modelo'));
    }

    private function adiciona_request(Request $request)
    {
        if (empty($request->codregra1)) {
            $request->request->add([
                'codusuario' => Auth::user()->codusuario,
                'codregra1' => 0
            ]);
        } else {
            $request->request->add([
                'codusuario' => Auth::user()->codusuario
            ]);
        }
    }

    private function msg($regra)
    {
        if (isset($regra)) {
            flash('Regra Criada com sucesso!!!');
        } else {
            flash('Regra Não Foi Criada com sucesso!!!');
        }
    }

    public function store(Request $request)
    {

        $projeto = Projeto::findOrFail($request->codprojeto);
        $organizacao = Organizacao::findOrFail($request->codorganizacao);
        $modelo = Modelo::findOrFail($request->codmodelo);

        $request->request->add([
            'codusuario' => Auth::user()->codusuario,
            'codregra1' => 0
        ]);

        $regra = Regra::create($request->all());

        self::msg($regra);
        return redirect()->route('controle_tarefas_create', [
            'codorganizacao' => $organizacao->codorganizacao,
            'codprojeto' => $projeto->codprojeto,
            'codmodelo' => $modelo->codmodelo,
            'codtarefa' => $regra->codregra
        ]);
    }

    public  function show($id)
    {
        $tarefa = Tarefa::findOrFail($id);
        return view('controle_tarefas.show', compact('tarefa'));
    }

    public  function edit($id)
    {
        $regra = Regra::findOrFail($id);
        $dados = Regra::dados();
        $dados[0]->valor = $regra->tarefas[0]->codtarefa;
        $dados[1]->valor = $regra->operador;
        $dados[2]->valor = $regra->tarefas[1]->codtarefa;
        $dados[3]->valor = $regra->nome;
        $organizacao = $regra->organizacao;
        $projeto = $regra->projeto;
        $modelo = $regra->modelo;
        $tarefas = TarefaRepository::listar();
        return view('controle_regras.edit', compact('dados', 'regra', 'organizacao', 'projeto', 'modelo', 'tarefas'));
    }


    private function set_param_regra(Request $request,Regra $regra){
        return [
            'nome' => $request->tarefa2_nome,
            'operador' => $request->tarefa2_descricao,
            'codmodelo' => $regra->codmodelo,


            'codprojeto' => $regra->codprojeto,
            'codorganizacao' => $regra->codorganizacao,
            'codusuario' => $regra->codusuario
        ];
    }

    public function update(Request $request, $codregra)
    {
        $regra = Regra::findOrFail($codregra);
        $regra->update($request->all());
        LogRepository::criar("Regra Atualizada Com sucesso", "Rota De Atualização de Regra");
        if (isset($tarefa)) {
            flash('Regra atualizada com sucesso!!');
        } else {
            flash('Regra não foi atualizada!!');
        }
        return redirect()->route('controle_regras_index', [
            'codorganizacao' => $regra->codorganizacao,
            'codprojeto' => $regra->codprojeto,
            'codmodelo' => $regra->codmodelo
        ]);
    }


    public  function destroy($codregra)
    {
        $regra = Regra::findOrFail($codregra);
        $projeto = $regra->projeto;
        $organizacao = $regra->organizacao;
        $modelo = $regra->modelo;
        try {
            $regra->delete();
            LogRepository::criar("Regra Excluída Com sucesso", "Rota De Exclusão de Regra");
            if (!$regra->exists) {
                flash('Regra excluída com sucesso!!');
            } else {
                flash('Regra não foi excluída!!')->warning();
            }
        } catch (\Exception $e) {
            flash('Error!!')->error();
        }
        if (!empty($projeto) || !empty($organizacao) && !empty($modelo)) {
            $titulos = Regra::titulos();
            $regras = Regra::join('users', 'users.codusuario', '=', 'regras.codusuario')->get();
            $tipo = 'regra';
            return view('controle_regras.all', compact('titulos', 'regras', 'tipo'));
        } else {
            return redirect()->route('controle_regras_index', [
                'codorganizacao' => $organizacao->codorganizacao,
                'codprojeto' => $projeto->codprojeto,
                'codmodelo' => $modelo->codmodelo
            ]);
        }

    }
}
