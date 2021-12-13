<?php

namespace App\Traits;

use Twig\Environment;
use Twig\TwigFunction;
use Twig\Loader\FilesystemLoader;

trait View
{
    protected $twig;

    protected function twigInit()
    {
        $loader = new FilesystemLoader("./../resources/views/");
        $this->twig = new Environment($loader, array(
            'cache' => false,
            'auto_reload' => true
        ));
    }

    protected function twigRegisterFunctions()
    {
        $this->twig->registerUndefinedFunctionCallback(function($name){
            if ( function_exists($name) ) {
                return new TwigFunction($name, function() use($name) {
                    return call_user_func_array($name, func_get_args());
                });
            }
            throw new \Exception("Função {$name} não encontrada.");
        });
    }

    public function viewRender( $file, $vars = [] )
    {
        $this->twigInit();
        $this->twigRegisterFunctions();

        $replaceFile = str_replace(".", "/", $file) . ".html";
        return $this->twig->render($replaceFile, $vars);
    }
}