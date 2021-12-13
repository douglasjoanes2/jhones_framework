<?php

ob_start();
session_start();

/**
 * Fuso horário brasileiro para funções de data e hora
 */
date_default_timezone_set("America/Sao_Paulo");

require_once "./../vendor/autoload.php";

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Carrega as rotas da aplicação
require_once "src/routes/web.php";

/**
 * Executa a aplicação
 */
if (($match = $router->match())) {

    $error = false;

    $controller = $match["target"]["controller"];
    $action     = $match["target"]["action"];
    $params     = $match["params"];
    $routeName  = $match["name"];
    $request    = new App\Http\Request();

    // Se o controller for uma função
    if (is_callable($controller)) {
        call_user_func($controller, [$request, $params]);
        exit;
    }

    // Se o controller for uma classe
    if (is_callable([$controller, $action])) {
        call_user_func_array([(new $controller($routeName)), $action], [$request, $params]);
        exit;
    }

    // 400
    $error = true;
} else {
    
    // 404
    $error = true;
}

if ( $error ) {
    $routeError = getRouteUrl("error", ["code" => 404]);
    header("location: {$routeError}");
}

ob_flush();