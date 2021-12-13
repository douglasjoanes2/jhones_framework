<?php

/**
 * Debug
 *
 * @param  mixed $data
 * @return string
 */
function dd($data)
{
    echo "<pre>";
    print_r($data);
    echo "</pre>";
    exit;
}

/**
 * Carrega arquivos auxiliares de template
 *
 * @param  string $path
 * @param  bool $time
 * @return string
 */
function asset(string $path, bool $time = true)
{
    $path      = trim($path, '/');
    $file      = $_ENV["SITE_URL"] . "/assets/{$path}";
    $fileOnDir = dirname(__DIR__, 1) . "/public/assets/{$path}";

    if ($time && file_exists($fileOnDir)) {
        $file .= "?time=" . filemtime($fileOnDir);
    }
    return $file;
}

/**
 * Valida se existe uma sessão de usuário
 *
 * @return bool
 */
function loggedUser()
{
    if (empty($_SESSION["user"])) {
        return false;
    }
    return true;
}