<?php

namespace App\Controllers;

use App\Http\Response;
use App\Http\Router;
use App\Traits\View;

abstract class Controller
{
    use View;

    /**
     * Nome da rota acessada
     *
     * @var mixed
     */
    protected $routeName;

    protected $seo;
 
    /**
     * __construct
     *
     * @return void
     */
    public function __construct($routeName)
    {
        $this->routeName = $routeName;
    }

    /**
     * Renderiza uma resposta do tipo HTML
     *
     * @param  string $name
     * @param  array $vars
     * @param  int $httpCode
     * @return string
     */
    public function renderView(string $name, array $vars = [], int $httpCode = 200)
    {
        $vars["site"]["name"] = $_ENV["SITE_NAME"];
        $vars["site"]["url"]  = $_ENV["SITE_URL"];

        if (!empty($_SESSION["user"])) {
            $vars["loggedUser"] = $_SESSION["user"];
        }

        $response = new Response();
        return $response->setContent($this->viewRender($name, $vars))
            ->setHttpCode($httpCode)
            ->sendResponse();
    }

    /**
     * Renderiza uma resposta do tipo JSON
     *
     * @param  array $content
     * @param  int $httpCode
     * @return string
     */
    public function renderJson(array $content, int $httpCode = 200)
    {
        $response = new Response();
        return $response->setContent(json_encode($content))
            ->setContentType("application/json")
            ->setHttpCode($httpCode)
            ->sendResponse();
    }


    /**
     * Retorna uma mensagem de erro para o ajax
     *
     * @param  string $msg
     * @return string
     */
    public function ajaxResponseDangerMessage(string $msg)
    {
        return $this->ajaxResponsetMessage($msg, "danger");
    }

    /**
     * Retorna uma mensagem de sucesso para o ajax
     *
     * @param  string $msg
     * @return string
     */
    public function ajaxResponseSuccessMessage(string $msg)
    {
        return $this->ajaxResponsetMessage($msg, "success");
    }

    /**
     * Retorna uma mensagem de alerta para o ajax
     *
     * @param  string $msg
     * @return string
     */
    public function ajaxResponseWarningMessage(string $msg)
    {
        return $this->ajaxResponsetMessage($msg, "warning");
    }

    /**
     * Retorna uma mensagem de informação para o ajax
     *
     * @param  string $msg
     * @return string
     */
    public function ajaxResponseInfoMessage(string $msg)
    {
        return $this->ajaxResponsetMessage($msg, "info");
    }

    /**
     * Retorna uma mensagem para o ajax
     *
     * @param  string $msg
     * @return string
     */
    public function ajaxResponsetMessage(string $msg, string $type = "success")
    {
        return $this->ajaxResponseData([
            "message" => [
                "type" => strtolower($type),
                "msg"  => $msg
            ]
        ]);
    }

    /**
     * Retorna dados para o ajax
     *
     * @param  mixed $data
     * @return string
     */
    public function ajaxResponseData($data)
    {
        return $this->renderJson($data);
    }

    /**
     * Retorna dados para o ajax
     *
     * @param  mixed $data
     * @return string
     */
    public function ajaxRedirect(string $url)
    {
        return $this->ajaxResponseData([
            "redirect" => ["url" => $url]
        ]);
    }

    /**
     * Redireciona para um determinada URL
     *
     * @param  string $url
     * @param  int $httpCode
     * @return void
     */
    public function redirect(string $url, int $httpCode = null)
    {
        if (!empty($httpCode)) {
            header("location: {$url}", true, $httpCode);
            exit;
        }
        header("location: {$url}");
        exit;
    }

    public function ajaxResponseMessage($message, $type = "success")
    {
        return $this->ajaxResponse([
            "message" => [
                "type" => $type,
                "msg"  => $message
            ]
        ]);
    }

    public function ajaxResponse($response)
    {
        return json_encode($response);
    }
}
