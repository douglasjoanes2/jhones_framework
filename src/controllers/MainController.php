<?php

namespace App\Controllers;

use App\Http\Request;

class MainController extends Controller
{
    /**
     * Renderiza a página inicial
     *
     * @return string
     */
    public function index()
    {
        $this->renderView("home");
    }

    /**
     * Renderiza a página de contato
     *
     * @return string
     */
    public function contact()
    {
        $this->renderView("contact");
    }

    /**
     * Renderiza a página de erro
     *
     * @param  Request $request
     * @param  array $args
     * @return string
     */
    public function error(Request $request, $args)
    {
        $msg = null;

        switch ($args["code"]) {
            case 400:
                $msg = "Bad request.";
                break;
            case 404:
                $msg = "A página que você procura não foi encontrada.";
                break;
        }
        $this->renderView("error", [
            "error" => [
                "code" => $args["code"],
                "msg"  => $msg
            ]
        ]);
    }

    /**
     * Encerra a sessão do usuário
     *
     * @return void
     */
    public function logout()
    {
        if (!empty($_SESSION["user"])) {
            unset($_SESSION["user"]);
        }
        session_destroy();
        $this->redirect(baseUrl());
    }
}
