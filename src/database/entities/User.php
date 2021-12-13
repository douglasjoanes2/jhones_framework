<?php

namespace App\Database\Entities;

use App\Database\DataLayer;
use Exception;

class User extends DataLayer
{
    public function __construct()
    {
        parent::__construct("user", ["name", "email", "passwd"]);
    }
    
    /**
     * Retorna um usuário comparando pelo email
     *
     * @param  string $email
     * @return object|null
     */
    public function findByEmail($email)
    {
        $res = $this->read("SELECT * FROM %s WHERE email=?", [$email], false);
        if (!$res) {
            return null;
        }
        return $res;
    }

    /**
     * Insere ou atualiza um usuário
     *
     * @return bool
     */
    public function save()
    {
        if (!$this->validateName() 
            || !$this->validateEmail() 
            || !$this->validatePassword() 
            || !parent::save()
        ) {
            return false;
        }
        return true;
    }

    protected function  validateName()
    {
        $name = explode(" ", trim($this->name));
        if (count($name) < 2) {
            $this->error = new Exception("Informe seu nome completo");
            return false;
        }

        $this->name    = array_shift($name);
        $this->surname = array_pop($name);
        return true;
    }

    /**
     * Valida o e-mail do usuário
     *
     * @return bool
     */
    protected function validateEmail()
    {
        // Verifica se o email informado é válido
        if (empty($this->email) || !filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $this->error = new Exception("Informe um e-mail válido");
            return false;
        }

        $userByEmail = null;
        if (!$this->id) {
            $userByEmail = $this->read("SELECT * FROM %s WHERE email=?", [$this->email], false);
        } else {
            $userByEmail = $this->read("SELECT * FROM %s WHERE email=? AND id!=?", [$this->email, $this->id], false);
        }

        // Verifica se o e-mail informado já está em uso
        if ($userByEmail) {
            $this->error = new Exception("O e-mail informado já esta em uso");
            return false;
        }

        return true;
    }

    /**
     * Valida a senha do usuário
     *
     * @return bool
     */
    protected function validatePassword()
    {
        if (empty($this->passwd) || strlen($this->passwd) < 6) {
            $this->error = new Exception("Informe uma senha com ao menos 6 caracteres");
            return false;
        }

        if (password_get_info($this->passwd)["algo"]) {
            return true;
        }

        $this->passwd = password_hash($this->passwd, PASSWORD_DEFAULT);
        return true;
    }

    public function login()
    {
        $user = $this->findByEmail($this->email);
        if ( empty($user) || !password_verify($this->passwd, $user->passwd) ) {
            $this->error = new Exception("Usuário e/ou senha inválidos");
            return false;
        }
        return $user->id;
    }
}
