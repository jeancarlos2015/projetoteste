<?php
/**
 * Created by PhpStorm.
 * User: jean
 * Date: 16/09/2018
 * Time: 18:06
 */

namespace App\Http\Fachadas;


use App\Http\Repositorys\BranchsRepository;
use App\Http\Repositorys\GitSistemaRepository;
use App\Http\Util\Dado;
use App\Http\Util\ValidacaoLogErros;
use Github\Exception\ApiLimitExceedException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use phpDocumentor\Reflection\Types\Self_;

class FachadaGit extends FachadaConcreta
{

    private function funcionalidades()
    {
        return [
            'Merge & checkout',
            'Create & Delete',
            'Commit Branch',
            'Pull & Push Repository',
            'Initialization Repository'
        ];
    }

    private function rotas()
    {
        return [
            'index_merge_checkout',
            'index_create_delete',
            'index_commit_branch',
            'index_pull_push',
            'index_init'
        ];
    }

    public function index_merge_checkout()
    {
        $branch_atual = 'Em construção';
        return view('controle_versao.merge_checkout', compact('branch_atual', 'branchs'));
    }

    public function index_create_delete()
    {
        $branch_atual = 'Em construção';
        return view('controle_versao.create_delete', compact('branch_atual', 'branchs'));
    }

    public function index_commit_branch()
    {
        $branch_atual = 'Em construção';
        return view('controle_versao.commit', compact('branch_atual', 'branchs'));
    }

    public function index_pull_push()
    {
        $branch_atual = 'Em construção';
        return view('controle_versao.pull_push', compact('branch_atual', 'branchs'));
    }

    public function index_init()
    {
        try {

            $branch_atual = 'Em construção';
            $repositorios = GitSistemaRepository::listar_repositorios();

            $tipo = 'repositorio_github';
            $titulos = [
                'Nome Do Repositório',
                'Nome Completo Do Repositório',
                'Ações'
            ];
        } catch (ApiLimitExceedException $ex) {
            $data['mensagem'] = $ex->getMessage();
            $data['tipo'] = 'error';
            $data['pagina'] = 'controle_versao.init';
            $data['acao'] = 'index';
            ValidacaoLogErros::create_log($data);
        } catch (\Exception $ex) {
            $data['mensagem'] = $ex->getMessage();
            $data['tipo'] = 'error';
            $data['pagina'] = 'controle_versao.init';
            $data['acao'] = 'index';
            ValidacaoLogErros::create_log($data);
        }
        return view('controle_versao.selecao_criacao_de_bases', compact('tipo', 'branch_atual', 'titulos', 'repositorios'));
    }


    public function index($codigo1 = null, $codigo2 = null)
    {
        $funcionalidades = [];
        $rotas = self::rotas();
        $dados = self::funcionalidades();
        for ($indice = 0; $indice < 5; $indice++) {
            $funcionalidades[$indice] = new Dado();
            $funcionalidades[$indice]->titulo = $dados[$indice];
            $funcionalidades[$indice]->rota = $rotas[$indice];
        }
        $branch_atual = 'Em construção';

        return view('controle_versao.index', compact('branch_atual', 'funcionalidades'));
    }

    public function selecionar_repositorio($repositorio_atual, $default_branch)
    {
        try {
            $dado['default_branch'] = $default_branch;
            $dado['repositorio_atual'] = $repositorio_atual;
            FachadaRepositorio::selecionar_repositorio($dado);
        } catch (ApiLimitExceedException $ex) {
            $data['mensagem'] = $ex->getMessage();
            $data['tipo'] = 'error';
            $data['pagina'] = 'controle_versao.init';
            $data['acao'] = 'init';
            ValidacaoLogErros::create_log($data);
        } catch (\Exception $ex) {
            $data['mensagem'] = $ex->getMessage();
            $data['tipo'] = 'error';
            $data['pagina'] = 'controle_versao.init';
            $data['acao'] = 'init';
            ValidacaoLogErros::create_log($data);
        }

        return redirect()->route('controle_versao.show', ['nome_repositorio' => $repositorio_atual]);
    }

    public function criar_base(Request $request)
    {
        try {
            $repositorio = GitSistemaRepository::create_repository($request->nome);
            GitSistemaRepository::atualizar_usuario_github($repositorio);
            return redirect()->route('controle_versao.show', ['nome_repositorio' => $request->nome]);
        } catch (ApiLimitExceedException $ex) {
            $data['mensagem'] = $ex->getMessage();
            $data['tipo'] = 'error';
            $data['pagina'] = 'controle_versao.init';
            $data['acao'] = 'init';
            ValidacaoLogErros::create_log($data);
        } catch (\Exception $ex) {
            $data['mensagem'] = $ex->getMessage();
            $data['tipo'] = 'error';
            $data['pagina'] = 'controle_versao.init';
            $data['acao'] = 'init';
            ValidacaoLogErros::create_log($data);
        }
        return redirect()->route('index_init');

    }

    public function show($nome_repositorio = null)
    {
        try {
            $repositorio = GitSistemaRepository::get_repositorio($nome_repositorio);
        } catch (ApiLimitExceedException $ex) {
            $data['mensagem'] = $ex->getMessage();
            $data['tipo'] = 'error';
            $data['pagina'] = 'controle_versao.show';
            $data['acao'] = 'show';
            ValidacaoLogErros::create_log($data);
        } catch (\Exception $ex) {
            $data['mensagem'] = $ex->getMessage();
            $data['tipo'] = 'error';
            $data['pagina'] = 'controle_versao.show';
            $data['acao'] = 'show';
            ValidacaoLogErros::create_log($data);
        }
        return view('controle_versao.visualicao_da_base', compact('tipo', 'branch_atual', 'repositorio'));

    }

    public function delete_repository($repositorio_atual)
    {
        try {
            GitSistemaRepository::delete_repository($repositorio_atual);
        } catch (ApiLimitExceedException $ex) {
            $data['mensagem'] = $ex->getMessage();
            $data['tipo'] = 'error';
            $data['pagina'] = 'Painel';
            $data['acao'] = 'delete_repository';
            ValidacaoLogErros::create_log($data);
        } catch (\Exception $ex) {
            $data['mensagem'] = $ex->getMessage();
            $data['tipo'] = 'error';
            $data['pagina'] = 'Painel';
            $data['acao'] = 'delete_repository';
            ValidacaoLogErros::create_log($data);
        }
        return redirect()->route('index_init');
    }

    public function edit_repository(Request $request)
    {
        echo 'pagina em construção';
        return null;
    }

    public function delete(Request $request)
    {
        try {

            BranchsRepository::excluir_branch($request->branch);
            $data['tipo'] = 'success';
            ValidacaoLogErros::create_log($data);
        } catch (ApiLimitExceedException $ex) {
            $data['mensagem'] = $ex->getMessage();
            $data['tipo'] = 'error';
            $data['pagina'] = 'Painel';
            $data['acao'] = 'delete';
            ValidacaoLogErros::create_log($data);
        } catch (\Exception $ex) {
            $data['mensagem'] = $ex->getMessage();
            $data['tipo'] = 'error';
            $data['pagina'] = 'Painel';
            $data['acao'] = 'delete';
            ValidacaoLogErros::create_log($data);
        }
        return redirect()->route('painel');
    }

    public function create(Request $request = null, $codigo = null)
    {
        try {
            $request->request->add([
                'descricao' => 'nenhum',
                'cod_usuario' => Auth::user()->cod_usuario
            ]);
            BranchsRepository::incluir($request->all());
            $data['tipo'] = 'success';
            ValidacaoLogErros::create_log($data);
        } catch (ApiLimitExceedException $ex) {
            $data['mensagem'] = $ex->getMessage();
            $data['tipo'] = 'error';
            $data['pagina'] = 'Painel';
            $data['acao'] = 'create';
            ValidacaoLogErros::create_log($data);
        } catch (\Exception $ex) {
            $data['mensagem'] = $ex->getMessage();
            $data['tipo'] = 'error';
            $data['pagina'] = 'Painel';
            $data['acao'] = 'create';
            ValidacaoLogErros::create_log($data);
        }
        return redirect()->route('painel');
    }

    /* Atualizar_todas_branchs: -deleta as branchs que existem no banco de acordo com o repositório fornecido.
                                -busca todas as branchs que existem no repositório fornecido.
                                -Inclui as branchs no banco.

       Pull:                    -Verifica se existem arquivos locais, baixa os arquivos que existem no repositório
                                -Atualiza os arquivos locais de acordo com o repositório e a branch fornecida
     *
     *
     */
    public function pull()
    {
        try {
            GitSistemaRepository::pull(Auth::user()->github->branch_atual);
            $data['tipo'] = 'success';
            ValidacaoLogErros::create_log($data);
        } catch (ApiLimitExceedException $ex) {
            $data['mensagem'] = $ex->getMessage();
            $data['tipo'] = 'error';
            $data['pagina'] = 'Painel';
            $data['acao'] = 'pull';
            ValidacaoLogErros::create_log($data);
        } catch (\Exception $ex) {
            $data['mensagem'] = $ex->getMessage();
            $data['tipo'] = 'error';
            $data['pagina'] = 'Painel';
            $data['acao'] = 'pull';
            ValidacaoLogErros::create_log($data);
        }

        return redirect()->route('painel');
    }

    private function merge_checkout_administrador(Request $request)
    {
        try {
            $validate = [
                'branch',
                'tipo'
            ];
            $erros = \Validator::make($request->all(), $validate);
            if ($erros->fails()) {
                return redirect()->route('painel')
                    ->withErrors($erros)
                    ->withInput();
            } else {
                GitSistemaRepository::merge_checkout($request->tipo, $request->branch);
                $data['tipo'] = 'success';
                ValidacaoLogErros::create_log($data);

            }

        } catch (ApiLimitExceedException $ex) {
            $data['mensagem'] = $ex->getMessage();
            $data['tipo'] = 'error';
            $data['pagina'] = 'Painel';
            $data['acao'] = 'merge_checkout';
            ValidacaoLogErros::create_log($data);
        } catch (\Exception $ex) {
            $data['mensagem'] = $ex->getMessage();
            $data['tipo'] = 'error';
            $data['pagina'] = 'Painel';
            $data['acao'] = 'merge_checkout';
            ValidacaoLogErros::create_log($data);
        }
        return redirect()->route('painel');
    }

    private function merge_checkout_usuario(Request $request)
    {

        try {
            $validate = [
                'branch',
                'tipo'
            ];
            $erros = \Validator::make($request->all(), $validate);
            if ($erros->fails()) {
                return redirect()->route('painel')
                    ->withErrors($erros)
                    ->withInput();
            } else {
                if ($request->branch !== 'master') {

                    GitSistemaRepository::merge_checkout($request->tipo, $request->branch);

                    $data['tipo'] = 'success';
                    ValidacaoLogErros::create_log($data);
                } else {
                    $data['mensagem'] = 'Este usuário não pode mecher na versão oficial do projeto';
                    $data['tipo'] = 'success';
                    ValidacaoLogErros::create_log($data);
                }


            }

        } catch (ApiLimitExceedException $ex) {
            $data['mensagem'] = $ex->getMessage();
            $data['tipo'] = 'error';
            $data['pagina'] = 'Painel';
            $data['acao'] = 'merge_checkout';
            ValidacaoLogErros::create_log($data);
        } catch (\Exception $ex) {
            $data['mensagem'] = $ex->getMessage();
            $data['tipo'] = 'error';
            $data['pagina'] = 'Painel';
            $data['acao'] = 'merge_checkout';
            ValidacaoLogErros::create_log($data);
            return redirect()->route('painel');
        }
        return redirect()->route('painel');
    }

    public function merge_checkout(Request $request)
    {
        if (Auth::user()->email === 'jeancarlospenas25@gmail.com') {
            self::merge_checkout_administrador($request);
        } else {
            self::merge_checkout_usuario($request);
        }
        return null;
    }


    public function commit(Request $request)
    {
        try {

            GitSistemaRepository::commit($request->commit);
            $data['tipo'] = 'success';
            ValidacaoLogErros::create_log($data);
        } catch (ApiLimitExceedException $ex) {
            $data['mensagem'] = $ex->getMessage();
            $data['tipo'] = 'error';
            $data['pagina'] = 'Painel';
            $data['acao'] = 'commit';
            ValidacaoLogErros::create_log($data);
        } catch (\Exception $ex) {
            $data['tipo'] = 'success';
            ValidacaoLogErros::create_log($data);
        }
        return redirect()->route('painel');
    }

}