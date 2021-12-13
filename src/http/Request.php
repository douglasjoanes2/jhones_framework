<?php

namespace App\Http;

class Request
{
    /**
     * Método http da requisição.
     *
     * @var string
     */
    private $httpMethod;

    /**
     * Uri da página.
     *
     * @var string
     */
    private $uri;

    /**
     * Parâmetros da url;
     *
     * @var array
     */
    private $queryParams = [];

    /**
     * Variáveis recebidas do post da página.
     *
     * @var array
     */
    private $postVars = [];

    /**
     * Cabeçalho da requisição.
     *
     * @var array
     */
    private $headers = [];

    /**
     * Construtor da classe.
     *
     * @return void
     */
    public function __construct()
    {
        $this->queryParams  = filter_input_array(INPUT_GET, FILTER_DEFAULT);
        $this->postVars     = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        $this->headers      = getallheaders();
        $this->httpMethod   = $_SERVER['REQUEST_METHOD'] ?? [];
        $this->uri          = $_SERVER['REQUEST_URI'] ?? [];
    }
    
    /**
     * Retorna o metodo http da requisição.
     *
     * @return string
     */
    public function getHttpMethod()
    {
        return $this->httpMethod;
    }
    
    /**
     * Retorna a Uri da requisição.
     *
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }
    
    /**
     * Retorna os headers da requisição.
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }
        
    /**
     * Retorna as variáveis do tipo POST
     *
     * @return array
     */
    public function getPostVars()
    {
        return $this->postVars;
    }
        
    /**
     * Retorna as variáveis do tipo GET
     *
     * @return array
     */
    public function getQueryParams()
    {
        return $this->queryParams;
    }
}