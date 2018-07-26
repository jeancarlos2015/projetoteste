<?php

namespace App\Http\Controllers;

use App\Http\Models\Modelo;
use App\Http\Models\Organizacao;
use App\Http\Models\Projeto;
use App\Http\Repositorys\LogRepository;
use App\Http\Repositorys\ModeloRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ModeloController extends Controller
{

    public function index($codorganizacao, $codprojeto)
    {
        try {
            $projeto = Projeto::findOrFail($codprojeto);
            $organizacao = Organizacao::findOrFail($codorganizacao);
            $titulos = Modelo::titulos();
            $modelos = ModeloRepository::listar_modelo_por_projeto_organizacao($codorganizacao, $codprojeto);
        } catch (\Exception $ex) {
            $codigo = LogRepository::criar(
                $ex->getMessage(),
                'warning',
                'index',
                'index/listar');
            flash('Atenção - Log Número ' . $codigo . " Favor consultar no Logs do Sistema")->warning();
        }
        $tipo = 'modelo';
        return view('controle_modelos.index', compact('modelos', 'projeto', 'organizacao', 'titulos', 'tipo'));
    }

    public function todos_modelos()
    {
        try {
            $modelos = ModeloRepository::listar();
            $titulos = Modelo::titulos();
            $tipo = 'modelo';
            $log = LogRepository::log();
        } catch (\Exception $ex) {
            $codigo = LogRepository::criar(
                $ex->getMessage(),
                'warning',
                'index',
                'todos_modelos');
            flash('Atenção - Log Número ' . $codigo . " Favor consultar no Logs do Sistema")->warning();
        }
        return view('controle_modelos.index_todos_modelos', compact('modelos', 'titulos', 'tipo', 'log'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function escolhe_modelo(Request $request)
    {
        $dado['tipo'] = $request->tipo;
        $dado['nome'] = $request->nome;
        $dado['descricao'] = $request->descricao;
        $dado['codprojeto'] = $request->codprojeto;
        $dado['codorganizacao'] = $request->codorganizacao;
        try {
            $projeto = Projeto::findOrFail($request->codprojeto);
            $organizacao = Organizacao::findOrFail($request->codorganizacao);
        } catch (\Exception $ex) {
            $codigo = LogRepository::criar(
                $ex->getMessage(),
                'warning',
                'form - modelos',
                'escolher_modelo');
            flash('Atenção - Log Número ' . $codigo . " Favor consultar no Logs do Sistema")->warning();
        }
        return view('controle_modelos.create', compact('dado', 'projeto', 'organizacao'));
    }

    public function create($codorganizacao, $codprojeto)
    {
        try {
            $projeto = Projeto::findOrFail($codprojeto);
            $organizacao = Organizacao::findOrFail($codorganizacao);
            $dados = Modelo::dados();
        } catch (\Exception $ex) {
            $codigo = LogRepository::criar(
                $ex->getMessage(),
                'warning',
                'form - modelos',
                'create');
            flash('Atenção - Log Número ' . $codigo . " Favor consultar no Logs do Sistema")->warning();
        }
        return view('controle_modelos.create', compact('dados', 'organizacao', 'projeto'));
    }

    private function valida_redireciona(Request $request, $codorganizacao, $codprojeto){
        $request->request->add([
            'xml_modelo' => 'nenhum',
            'codusuario' => Auth::user()->codusuario
        ]);

        $erros = \Validator::make($request->all(), Modelo::validacao());
        if ($erros->fails()) {
            return redirect()->route('controle_modelos_create', [
                'codorganizacao' => $codorganizacao,
                'codprojeto' => $codprojeto
            ])
                ->withErrors($erros)
                ->withInput();
        }
    }
    private function valida_tipo_redireciona($modelo){
        if ($modelo->tipo === 'declarativo') {
            flash('Modelos criado com sucesso!!!');
            return redirect()->route('controle_regras_index',
                [
                    'codorganizacao' => $modelo->codorganizacao,
                    'codprojeto' => $modelo->codprojeto,
                    'codmodelo' => $modelo->codmodelo
                ]);
        } else {
            return view('controle_modelos.form_diagramatico', compact('modelo'));
        }
    }
    public function store(Request $request)
    {
        try {
            $codprojeto = $request->codprojeto;
            $codorganizacao = $request->codorganizacao;
             $this->valida_redireciona($request, $codorganizacao, $codprojeto);
            $modelo = Modelo::create($request->all());
            $this->valida_tipo_redireciona($modelo);
        }
        catch (\Exception $ex) {
            $codigo = LogRepository::criar(
                $ex->getMessage(),
                'warning',
                'form - modelos',
                'store');
            flash('Atenção - Log Número ' . $codigo . " Favor consultar no Logs do Sistema")->warning();
        }
        return view('controle_modelos.form_diagramatico', compact('modelo'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($codmodelo)
    {
        try {
            $modelo = Modelo::findOrFail($codmodelo);
            $projeto = $modelo->projeto;
            $organizacao = $modelo->organizacao;
            if ($modelo->tipo === 'declarativo') {
                return view('controle_modelos.form_declarativo', compact(
                    'modelo',
                    'projeto',
                    'organizacao'
                ));
            } else {
                return view('controle_modelos.form_diagramatico', compact('modelo'));
            }
        } catch (\Exception $ex) {
            $codigo = LogRepository::criar(
                $ex->getMessage(),
                'warning',
                'controle_modelos.form_declrativo/form_diagramatico',
                'show');
            flash('Atenção - Log Número ' . $codigo . " Favor consultar no Logs do Sistema")->warning();
        }
        return redirect()->route('painel');


    }

    public function show_tarefas($codmodelo)
    {
        try{
        $modelo = Modelo::findOrFail($codmodelo);
        if (empty($modelo->codprojeto) || empty($modelo->codorganizacao)) {
            flash('Não existem tarefas para serem exibidas!!!')->error();
            return redirect()->route('controle_modelos.show', ['id' => $codmodelo]);
        } else {
            return redirect()->route('controle_tarefas_index', [
                'codorganizacao' => $modelo->codorganizacao,
                'codprojeto' => $modelo->codprojeto,
                'codmodelo' => $modelo->codmodelo
            ]);
        }
        } catch (\Exception $ex) {
            $codigo = LogRepository::criar(
                $ex->getMessage(),
                'warning',
                'controle_tarefas.index',
                'show_tarefas');
            flash('Atenção - Log Número ' . $codigo . " Favor consultar no Logs do Sistema")->warning();
        }
        return redirect()->route('painel');
    }

    public function show_regras($codmodelo)
    {
        try{
        $modelo = Modelo::findOrFail($codmodelo);
        if (empty($modelo->codprojeto) || empty($modelo->codorganizacao)) {
            flash('Não existem regras para serem exibidas!!!')->error();
            return redirect()->route('controle_modelos.show', ['id' => $codmodelo]);
        } else {
            return redirect()->route('controle_regras_index', [
                'codorganizacao' => $modelo->codorganizacao,
                'codprojeto' => $modelo->codprojeto,
                'codmodelo' => $modelo->codmodelo
            ]);
        }
        } catch (\Exception $ex) {
            $codigo = LogRepository::criar(
                $ex->getMessage(),
                'warning',
                'controle_regras_index',
                'show_regras');
            flash('Atenção - Log Número ' . $codigo . " Favor consultar no Logs do Sistema")->warning();
        }
        return redirect()->route('painel');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($codmodelo)
    {
        try{
        $modelo = Modelo::findOrFail($codmodelo);
        $dados = Modelo::dados();
        $projeto = $modelo->projeto;
        $organizacao = $modelo->organizacao;
        $dados[0]->valor = $modelo->nome;
        $dados[1]->valor = $modelo->descricao;
        $dados[2]->valor = $modelo->tipo;
        return view('controle_modelos.edit', compact('dados', 'modelo', 'projeto', 'organizacao'));
        } catch (\Exception $ex) {
            $codigo = LogRepository::criar(
                $ex->getMessage(),
                'warning',
                'edit',
                'edit');
            flash('Atenção - Log Número ' . $codigo . " Favor consultar no Logs do Sistema")->warning();
        }
        return redirect()->route('painel');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try{
        $modelo = Modelo::findOrFail($id);
        $modelo->update($request->all());
        if (isset($modelo)) {
            flash('Modelos atualizado com sucesso!!');
        } else {
            flash('Modelos não foi atualizado!!');
        }
        return redirect()->route('controle_tarefas_index', [
            'codorganizacao' => $modelo->codorganizacao,
            'codprojeto' => $modelo->codprojeto,
            'codmodelo' => $modelo->codmodelo
        ]);
        } catch (\Exception $ex) {
            $codigo = LogRepository::criar(
                $ex->getMessage(),
                'warning',
                'edit/update',
                'update');
            flash('Atenção - Log Número ' . $codigo . " Favor consultar no Logs do Sistema")->warning();
        }
        return redirect()->route('painel');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */

    private function delete($codmodelo)
    {   try{
        $modelo = ModeloRepository::excluir($codmodelo);
        return $modelo;
    } catch (\Exception $ex) {
        $codigo = LogRepository::criar($ex->getMessage(), 'warning');
        flash('Atenção - Log Número ' . $codigo . " Favor consultar no Logs do Sistema")->warning();
        return redirect()->route('painel');
    }
    }

    public function destroy($codprojeto)
    {
        try{
        $modelo = Modelo::findOrFail($codprojeto);

        $this->delete($modelo);
        if (empty($modelo->codprojeto) || empty($modelo->codorganizacao)) {

            return redirect()->route('todos_modelos');
        } else {
            return redirect()->route('controle_modelos_index',
                [
                    'codorganizacao' => $modelo->codorganizacao,
                    'codprojeto' => $modelo->codprojeto
                ]
            );
        }
        } catch (\Exception $ex) {
            $codigo = LogRepository::criar(
                $ex->getMessage(),
                'warning',
                'todos_modelos',
                'delete/destroy');
            flash('Atenção - Log Número ' . $codigo . " Favor consultar no Logs do Sistema")->warning();
        }
        return redirect()->route('painel');

    }
}
