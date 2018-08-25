<?php

use Faker\Generator as Faker;

//'codrepositorio',
//'codusuario',
//'codprojeto',
//'codmodelodeclarativo',
//'codoutraregra',
//'nome',
//'tipo',
//'visivel_projeto',
//'visivel_repositorio',
//'visivel_modelo_declarativo',
//'relacionamento'


$factory->define(\App\http\Models\Regra::class, function (Faker $faker) {
    return [
        'codrepositorio' => rand(1,49),
        'codusuario' => rand(1,49),
        'codprojeto' => rand(1,49),
        'codmodelodeclarativo' => rand(1,49),
        'codoutraregra' => null,
        'nome' => $faker->word,
        'tipo' => 'regra',
        'visivel_projeto' => 'true',
        'visivel_repositorio' => 'true',
        'visivel_modelo_declarativo' => 'true',
        'relacionamento' => rand(0,4)
    ];
});