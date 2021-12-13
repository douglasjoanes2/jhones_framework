<?php

namespace App\Controllers;

use App\Database\Entities\User;

class UserController extends Controller
{
    protected $user;

    public function __construct($routeName)
    {
        $this->user = (new User)->findByPrimaryKey($_SESSION["user"]["id"]);
        parent::__construct($routeName);

        if (empty($_SESSION["user"])) {
            $this->redirect(loginUrl());
        }
    }

    public function dashboard()
    {
        $this->renderView("user.dashboard", ["user" => $this->user]);
    }
}