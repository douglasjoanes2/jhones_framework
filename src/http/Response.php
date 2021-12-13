<?php

namespace App\Http;

class Response
{
    protected $httpCode = 200;

    protected $headers = [];

    protected $contentType = "text/html";

    protected $content;

    public function setContent( $content )
    {
        $this->content = $content;
        return $this;
    }

    public function setContentType( string $contentType )
    {
        $this->contentType  = $contentType;
        $this->addHeader("Content-Type", $contentType);
        return $this;
    }

    public function setHttpCode( int $httpCode )
    {
        $this->httpCode = $httpCode;
        return $this;
    }

    public function addHeader( $key, $value )
    {
        $this->headers[$key] = $value;
        return $this;
    }

    public function sendResponse()
    {
        http_response_code($this->httpCode);
        
        foreach($this->headers as $k => $v) {
            header("{$k}: {$v}");
        };

        echo $this->content;
        exit;
    }
}