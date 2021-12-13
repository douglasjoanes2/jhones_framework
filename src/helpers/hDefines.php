<?php

use App\Http\Router;


/**
 * Url base
 */
function baseUrl()
{
    return $_ENV["SITE_URL"];
}

/**
 * Url genérica
 */
function getRouteUrl( $routeName, $vars = [] )
{
    return baseUrl() . Router::newInstance()->generate($routeName, $vars);
}

/**
 * Url de login do usuário
 */
function loginUrl()
{
    return getRouteUrl("web.login");
}

/**
 * Url de login do usuário
 */
function logoutUrl()
{
    return getRouteUrl("logout");
}

/**
 * Url de cadastro do usuário
 */
function registerUrl()
{
    return getRouteUrl("web.register");
}

/**
 * Url de recuperação de senha do usuário
 */
function forgotPasswordUrl()
{
    return getRouteUrl("web.forgot");
}

/**
 * Url de contato
 */
function contactUrl()
{
    return getRouteUrl("contact");
}

/**
 * Url do painel do usuário
 */
function userDashboardUrl()
{
    return getRouteUrl("user.dashboard");
}