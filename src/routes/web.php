<?php

$router = App\Http\Router::newInstance();

$router->get("", "MainController:index", "home");
$router->get("contact", "MainController:contact", "contact");
$router->get("logout", "MainController:logout", "logout");
$router->get("oops/[i:code]", "MainController:error", "error");

/**
 * Rotas de autenticação
 */
$router->group([
    "as"     => "web"
], function () use ($router) {

    $router->get("login", "AuthController:login", "login");
    $router->post("login", "AuthController:loginProcess");

    $router->get("register", "AuthController:register", "register");
    $router->post("register", "AuthController:registerProcess");

    $router->get("forgot", "AuthController:forgot", "forgot");
    $router->post("forgot", "AuthController:forgotProcess");

    $router->get("resend", "AuthController:resend", "resend");
    $router->post("resend", "AuthController:resendProcess");
});

/**
 * Rotas do usuário
 */
$router->group([
    "prefix" => "user",
    "as"     => "user"
], function () use ($router) {

    $router->get("dashboard", "UserController:dashboard", "dashboard");
});