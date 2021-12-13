<?php

namespace App\Controllers;

use App\Database\Entities\User;
use App\Http\Request;

class AuthController extends Controller
{
    public function __construct($routename)
    {
        parent::__construct($routename);

        if (!empty($_SESSION["user"])) {
            $this->redirect(userDashboardUrl());
        }
    }
    /**
     * Exibe a página de login
     *
     * @return string
     */
    public function login()
    {
        $this->renderView("auth.login");
    }

    public function loginProcess(Request $request, $args)
    {
        $data = filter_var_array($request->getPostVars(), FILTER_SANITIZE_STRIPPED);

        $email  = filter_var($data["email"], FILTER_VALIDATE_EMAIL);
        $passwd = filter_var($data["passwd"], FILTER_DEFAULT);

        if (!$email || !$passwd) {
            echo $this->ajaxResponse([
                "message" => [
                    "type" => "danger",
                    "msg"  => "Informe seu e-mail e senha para entrar"
                ]
            ]);
            return;
        }

        $user = (new User())->findByEmail($email);
        if (empty($user) || !password_verify($passwd, $user->passwd)) {
            echo $this->ajaxResponse([
                "message" => [
                    "type" => "danger",
                    "msg"  => "Usuário e/ou senha inválidos"
                ]
            ]);
            return;
        }

        $_SESSION["user"] = [
            "id"   => $user->id,
            "name" => $user->name,
            "role" => $user->role
        ];

        echo $this->ajaxResponse([
            "redirect" => [
                "url" => userDashboardUrl()
            ]
        ]);
        return;
    }

    /**
     * Exibe a página de cadastro
     *
     * @return string
     */
    public function register()
    {
        $this->renderView("auth.register", [
            "created_account" =>  $_GET["created_account"] ?? null
        ]);
    }
    
    /**
     * Processa o cadastro de usuário
     *
     * @param  Request $request
     * @param  array $args
     * @return string
     */
    public function registerProcess(Request $request, $args)
    {
        $data = filter_var_array($request->getPostVars(), FILTER_SANITIZE_STRIPPED);
        if (in_array("", $data)) {
            echo $this->ajaxResponse([
                "message" => [
                    "type" => "danger",
                    "msg"  => "Preencha os campos necessários"
                ]
            ]);
            return;
        }

        $user         = new User();
        $user->name   = $data["name"];
        $user->email  = $data["email"];
        $user->passwd = $data["passwd"];

        if (!$user->save()) {
            echo $this->ajaxResponse([
                "message" => [
                    "type" => "danger",
                    "msg"  => $user->error()->getMessage()
                ]
            ]);
            return;
        }

        echo $this->ajaxResponse([
            "redirect" => [
                "url" => registerUrl() . "?created_account=true"
            ]
        ]);
        return;
    }

    /**
     * Exibe a página de recuperação de senha
     *
     * @return string
     */
    public function forgot()
    {
        $this->renderView("auth.forgot");
    }

    /**
     * Exibe a página de troca de senha
     *
     * @return string
     */
    public function reset()
    {
        $this->renderView("auth.reset");
    }
}
