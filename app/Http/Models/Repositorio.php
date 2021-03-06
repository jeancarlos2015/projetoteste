<?php

namespace App\http\Models;

use App\Http\Util\Dado;
use App\User;
use App\Http\Models\Projeto;
use App\Http\Models\RepresentacaoDiagramatico;
use App\Http\Models\RepresentacaoDeclarativa;
use App\Http\Models\AtribuicaoProjetoUsuario;
use Illuminate\Database\Eloquent\Model;

class Repositorio extends Model
{
    protected $primaryKey = 'cod_repositorio';
    protected $table = 'repositorios';
    protected $fillable = [
        'nome',
        'descricao',
        'visibilidade',
        'publico'
    ];

    public static function validacao()
    {
        return [
            'nome' => 'required|max:50',
            'descricao' => 'required|max:255'
        ];
    }

    public static function titulos_da_tabela()
    {
        return [
//            'ID',
//            'Nome',
//            'Descrição',
            'Repositórios',
            'Ações'
        ];
    }

    public static function campos()
    {
        return [
            'Nome',
            'Descrição'
        ];
    }

    public static function types()
    {
        return [
            'text',
            'text'
        ];
    }

    public static function atributos_dos_campos()
    {
        return [

            'nome',
            'descricao'
        ];

    }

//Instancia todas as posições de memória que serão exibidas no título
    public static function dados_exibidos_no_titulo()
    {
        $dado = array();
        for ($indice = 0; $indice < 2; $indice++) {
            $dado[$indice] = new Dado();
        }
        return $dado;
    }

//Instancia somente os campos que serão exibidos no formulário e preenche os títulos da listagem

    public static function dados()
    {
        $campos = self::campos();
        $atributos = self::atributos_dos_campos();
        $dados = self::dados_exibidos_no_titulo();
        $types = self::types();
        $titulos = self::titulos_da_tabela();
        for ($indice = 0; $indice < 2; $indice++) {
            if ($indice < 2) {
                $dados[$indice]->campo = $campos[$indice];
                $dados[$indice]->atributo = $atributos[$indice];
                $dados[$indice]->type = $types[$indice];
            }
            $dados[$indice]->titulo = $titulos[$indice];

        }
        return $dados;
    }

    public function usuarios()
    {
        return $this->hasMany(User::class, 'cod_repositorio', 'cod_repositorio');
    }

    public function projetos()
    {
        return $this->hasMany(Projeto::class, 'cod_repositorio', 'cod_repositorio');
    }

    public function atribuicao_projeto_usuarios()
    {
        return $this->hasMany(AtribuicaoProjetoUsuario::class, 'cod_repositorio', 'cod_repositorio');
    }

    public function modelos_diagramaticos()
    {
        return $this->hasMany(RepresentacaoDiagramatico::class, 'cod_repositorio', 'cod_repositorio');
    }

    public function modelos_declarativos()
    {
        return $this->hasMany(RepresentacaoDeclarativa::class, 'cod_repositorio', 'cod_repositorio');
    }
    protected static function boot()
    {
        parent::boot();
        static::deleting(function ($projeto) { // before delete() method call this
            $projeto->projetos()->delete();
        });

        static::deleting(function ($modelo) { // before delete() method call this
            $modelo->modelos_diagramaticos()->delete();
        });


    }
}
