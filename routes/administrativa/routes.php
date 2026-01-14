<?php

Route::group([
    'prefix' => 'administrativos',
    'as' => 'administrativo_',
    'middleware' => ['can:access-resource,"administrativa"'],
], function(){
    require __DIR__ . '/administrativos.php';
});

Route::group([
    'prefix' => 'conceptos-de-acciones',
    'as' => 'concepto_de_accion_',
    'middleware' => ['can:access-resource,"administrativa"'],
], function(){
    require __DIR__ . '/conceptos_de_acciones.php';
});

Route::group([
    'prefix' => 'historial-de-acciones',
    'as' => 'historial_de_acciones_',
    'middleware' => ['can:access-resource,"administrativa"'],
], function(){
    require __DIR__ . '/historial_de_acciones.php';
});

Route::group([
    'prefix' => 'usuarios',
    'as' => 'usuario_',
    'middleware' => ['can:access-resource,"administrativa"'],
], function(){
    require __DIR__ . '/usuarios.php';
});

Route::group([
    'prefix' => 'validacion_pago',
    'as' => 'validacion_pago_',
    'middleware' => ['can:access-resource,"administrativa"'],
], function(){
    require __DIR__ . '/validacion_pago.php';
});

Route::group([
    'prefix' => 'solicitudes-prematricula',
    'as' => 'solicitudes_prematricula.',
    'middleware' => ['can:access-resource,"administrativa"'],
], function(){
    require __DIR__ . '/solicitudes_prematricula.php';
});