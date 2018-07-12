<?php

namespace App\Http\Repositorys;


use App\Http\Models\Modelo;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ModeloRepository extends Repository
{

    public function __construct()
    {
        $this->setModel(Modelo::class);
    }

    public static function listar()
    {
        return Cache::remember('listar_modelos', 2000, function () {
            return collect((new Modelo)->join('users', 'users.id', '=', 'modelos.user_id')
                ->get());
        });
    }

    public static function count()
    {
        return Cache::remember('listar_modelos', 2000, function () {
            return collect((new Modelo)->join('users', 'users.id', '=', 'modelos.user_id')
                ->get())->count();
        });
    }

    public static function atualizar(Request $request, $id)
    {
        $value = Modelo::findOrFail($id);
        $value->update($request->all());
        self::limpar_cache();
        return $value;
    }

    public static function limpar_cache()
    {
        Cache::forget('listar_modelos');
    }

    public static function incluir(Request $request)
    {
        $value = Modelo::create($request->all());
        self::limpar_cache();
        return $value;
    }

    public static function excluir($id)
    {
        $value = null;
        try {
            $doc = Modelo::findOrFail($id);
            $value = $doc->delete();
            self::limpar_cache();
        } catch (Exception $e) {

        }
        return $value;
    }

}