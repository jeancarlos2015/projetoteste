<?php

namespace App\Http\Models;

use App\Http\Util\Dado;
use Illuminate\Database\Eloquent\Model;

class Regra extends Model
{
    protected $primaryKey = 'codregra';
    protected $table = 'regras';
    protected $fillable = [
        'operador',
        'nome',
        'codtarefa',

        'codmodelo',
        'codprojeto',
        'codorganizacao',
        'codregra1',
        'codusuario'
    ];

    public static function titulos()
    {
        return [
            'ID',
            'Tarefa 1',
            'Operador',
            'Tarefa 2',
            'Ações',
        ];
    }

    public static function campos()
    {
        return [
            'Tarefa 1',
            'Operador',
            'Tarefa 2',
            'Nome da Regra'
        ];
    }

    public static function atributos()
    {
        return [
            'tarefa1_id',
            'operador',
            'tarefa2_id',
            'nome'
        ];

    }

    public static function dados_objeto()
    {
        $dado = array();
        for ($indice = 0; $indice < 7; $indice++) {
            $dado[$indice] = new Dado();
        }
        return $dado;
    }

    public static function dados()
    {
        $campos = self::campos();
        $atributos = self::atributos();
        $dados = self::dados_objeto();
        $titulos = self::titulos();
        for ($indice = 0; $indice < 4; $indice++) {
            if ($indice < 4) {
                $dados[$indice]->campo = $campos[$indice];
                $dados[$indice]->atributo = $atributos[$indice];
            }
            $dados[$indice]->titulo = $titulos[$indice];

        }
        return $dados;
    }

    public  function tarefas()
    {
        return $this->hasOne(Tarefa::class, 'codtarefa', 'codtarefa');
    }

    public  function modelo()
    {
        return $this->hasOne(Modelo::class, 'codmodelo', 'codmodelo');
    }

    public  function projeto()
    {
        return $this->hasOne(Projeto::class, 'codprojeto', 'codprojeto');
    }

    public  function organizacao()
    {
        return $this->hasOne(Organizacao::class, 'codorganizacao', 'codorganizacao');
    }
}
