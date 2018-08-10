<?php

namespace App\Http\Controllers;

use App\Http\Models\Modelo;
use App\Http\Models\Repositorio;
use App\Http\Models\Projeto;
use App\Http\Repositorys\LogRepository;
use App\Http\Repositorys\ModeloRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ModeloController extends Controller
{

    public function index($codrepositorio, $codprojeto, $codusuario)
    {
        try {
            $projeto = Projeto::findOrFail($codprojeto);
            $repositorio = Repositorio::findOrFail($codrepositorio);
            $titulos = Modelo::titulos();
            $modelos = ModeloRepository::listar_modelo_por_projeto_organizacao($codrepositorio, $codprojeto, $codusuario);
        } catch (\Exception $ex) {
            $data['mensagem'] = $ex->getMessage();
            $data['tipo'] = 'error';
            $data['pagina'] = 'Painel';
            $data['acao'] = 'merge_checkout';
            $this->create_log($data);
        }
        $tipo = 'modelo';
        return view('controle_modelos.index', compact('modelos', 'projeto', 'repositorio', 'titulos', 'tipo'));
    }

    public function todos_modelos()
    {
        try {
            $modelos = ModeloRepository::listar();
            $titulos = Modelo::titulos();
            $tipo = 'modelo';
            $log = LogRepository::log();
        } catch (\Exception $ex) {
            $data['mensagem'] = $ex->getMessage();
            $data['tipo'] = 'error';
            $data['pagina'] = 'Painel';
            $data['acao'] = 'merge_checkout';
            $this->create_log($data);
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
        $dado['codrepositorio'] = $request->codrepositorio;
        try {
            $projeto = Projeto::findOrFail($request->codprojeto);
            $repositorio = Repositorio::findOrFail($request->codrepositorio);
        } catch (\Exception $ex) {
            $data['mensagem'] = $ex->getMessage();
            $data['tipo'] = 'error';
            $data['pagina'] = 'Painel';
            $data['acao'] = 'merge_checkout';
            $this->create_log($data);
        }
        return view('controle_modelos.create', compact('dado', 'projeto', 'repositorio'));
    }

    public function create($codrepositorio, $codprojeto)
    {
        try {
            $projeto = Projeto::findOrFail($codprojeto);
            $repositorio = Repositorio::findOrFail($codrepositorio);
            $dados = Modelo::dados();
        } catch (\Exception $ex) {
            $data['mensagem'] = $ex->getMessage();
            $data['tipo'] = 'error';
            $data['pagina'] = 'Painel';
            $data['acao'] = 'merge_checkout';
            $this->create_log($data);
        }
        return view('controle_modelos.create', compact('dados', 'repositorio', 'projeto'));
    }

    public function edicao_modelo_diagramatico($codmodelo)
    {
        $modelo = Modelo::findOrFail($codmodelo);
        $path_modelo = public_path('novo_bpmn/');
        if (!file_exists($path_modelo)) {
            mkdir($path_modelo, 777);
        }
        $file = $path_modelo . 'novo.bpmn';
        file_put_contents($file, $modelo->xml_modelo);
        sleep(2);
        return view('controle_modelos.modeler', compact('modelo'));
    }

//$codrepositorio, $codprojeto, $codmodelo
    public
    function store(Request $request)
    {
        try {
            $codprojeto = $request->codprojeto;
            $codrepositorio = $request->codrepositorio;
            $data['all'] = $request->all();
            $data['validacao'] = Modelo::validacao();
            if (!$this->exists_errors($data)) {
                if (!ModeloRepository::modelo_existe($request->nome)) {
                    $request->request->add([
                        'xml_modelo' => Modelo::get_modelo_default(),
                        'codprojeto' => $codprojeto,
                        'codrepositorio' => $codrepositorio,
                        'codusuario' => Auth::user()->codusuario
                    ]);
                    $modelo = Modelo::create($request->all());
                    if ($modelo->tipo === 'declarativo') {

                    } else {
                        return redirect()->route('edicao_modelo_diagramatico',
                            ['codmodelo' => $modelo->codmodelo]);
                    }
                } else {
                    $data['tipo'] = 'existe';
                    $this->create_log($data);
                    return redirect()->route('controle_modelos_create', [
                        'codrepositorio' => $codrepositorio,
                        'codprojeto' => $codprojeto
                    ]);
                }

            }
            $erros = $this->get_errors($data);
            return redirect()->route('controle_modelos_create', [
                'codrepositorio' => $codrepositorio,
                'codprojeto' => $codprojeto
            ])
                ->withErrors($erros)
                ->withInput();
        } catch (\Exception $ex) {
            $data['mensagem'] = $ex->getMessage();
            $data['tipo'] = 'error';
            $data['pagina'] = 'Painel';
            $data['acao'] = 'merge_checkout';
            $this->create_log($data);
        }

        return view('controle_modelos.modeler', compact('modelo'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public
    function show($codmodelo)
    {
        try {
            $modelo = Modelo::findOrFail($codmodelo);
            $projeto = $modelo->projeto;
            $repositorio = $modelo->repositorio;
            if ($modelo->tipo === 'declarativo') {
                return view('controle_modelos.form_declarativo', compact(
                    'modelo',
                    'projeto',
                    'repositorio'
                ));
            } else {
                $path_modelo = public_path('novo_bpmn/');
                if (!file_exists($path_modelo)) {
                    mkdir($path_modelo, 777);
                }
                $file = $path_modelo . 'novo.bpmn';
                file_put_contents($file, $modelo->xml_modelo);
                sleep(2);
                return view('controle_modelos.visualizar_modelo', compact('modelo'));
            }
        } catch (\Exception $ex) {
            $data['mensagem'] = $ex->getMessage();
            $data['tipo'] = 'error';
            $data['pagina'] = 'Painel';
            $data['acao'] = 'merge_checkout';
            $this->create_log($data);
        }
        return redirect()->route('painel');


    }

    public
    function show_tarefas($codmodelo)
    {
        try {
            $modelo = Modelo::findOrFail($codmodelo);
            if (empty($modelo->codprojeto) || empty($modelo->codrepositorio)) {
                flash('Não existem tarefas para serem exibidas!!!')->error();
                return redirect()->route('controle_modelos.show', ['id' => $codmodelo]);
            } else {
                return redirect()->route('controle_tarefas_index', [
                    'codrepositorio' => $modelo->codrepositorio,
                    'codprojeto' => $modelo->codprojeto,
                    'codmodelo' => $modelo->codmodelo
                ]);
            }
        } catch (\Exception $ex) {
            $data['mensagem'] = $ex->getMessage();
            $data['tipo'] = 'error';
            $data['pagina'] = 'Painel';
            $data['acao'] = 'merge_checkout';
            $this->create_log($data);
        }
        return redirect()->route('painel');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public
    function edit($codmodelo)
    {
        try {
            $modelo = Modelo::findOrFail($codmodelo);

            $dados = Modelo::dados();
            $projeto = $modelo->projeto;
            $repositorio = $modelo->repositorio;

            $dados[0]->valor = $modelo->nome;
            $dados[1]->valor = $modelo->descricao;
            $dados[2]->valor = $modelo->tipo;

            return view('controle_modelos.edit', compact('dados', 'modelo', 'projeto', 'repositorio'));
        } catch (\Exception $ex) {
            $data['mensagem'] = $ex->getMessage();
            $data['tipo'] = 'error';
            $data['pagina'] = 'Painel';
            $data['acao'] = 'merge_checkout';
            $this->create_log($data);
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
    public
    function update(Request $request, $id)
    {
        try {
            $modelo = Modelo::findOrFail($id);
            $modelo->update($request->all());
            if ($modelo->tipo === 'diagramatico') {
                return redirect()->route('edicao_modelo_diagramatico', [
                    'codmodelo' => $modelo->codmodelo
                ]);
            } else {
                return redirect()->route('controle_tarefas_index', [
                    'codrepositorio' => $modelo->codrepositorio,
                    'codprojeto' => $modelo->codprojeto,
                    'codmodelo' => $modelo->codmodelo
                ]);
            }

        } catch (\Exception $ex) {
            $data['mensagem'] = $ex->getMessage();
            $data['tipo'] = 'error';
            $data['pagina'] = 'Painel';
            $data['acao'] = 'merge_checkout';
            $this->create_log($data);
        }
        return redirect()->route('painel');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */

    private
    function delete($codmodelo)
    {
        try {
            $modelo = ModeloRepository::excluir($codmodelo);
            return $modelo;
        } catch (\Exception $ex) {
            $data['mensagem'] = $ex->getMessage();
            $data['tipo'] = 'error';
            $data['pagina'] = 'Painel';
            $data['acao'] = 'merge_checkout';
            $this->create_log($data);
        }
    }

    public
    function destroy($codprojeto)
    {
        try {

            $modelo = Modelo::findOrFail($codprojeto);
            $modelo->delete();
            flash('Operação feita com sucesso!!');
            if (empty($modelo->codprojeto) || empty($modelo->codrepositorio)) {

                return redirect()->route('todos_modelos');
            } else {
                return redirect()->route('controle_modelos_index',
                    [
                        'codrepositorio' => $modelo->codrepositorio,
                        'codprojeto' => $modelo->codprojeto
                    ]
                );
            }
        } catch (\Exception $ex) {
            $data['mensagem'] = $ex->getMessage();
            $data['tipo'] = 'error';
            $data['pagina'] = 'Painel';
            $data['acao'] = 'merge_checkout';
            $this->create_log($data);
        }
        return redirect()->route('painel');

    }
}
