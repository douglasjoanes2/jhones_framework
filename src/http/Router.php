<?php

namespace App\Http;

use AltoRouter;

class Router extends AltoRouter
{
    public static $instance;

    protected $separator = ':';

    private $groupStack = [];

    public static function newInstance()
    {
        if ( !self::$instance instanceof self ) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * Adiciona uma rota do tipo GET
     *
     * @param  string $route
     * @param  mixed $target
     * @param  string $name
     * @return void
     */
    public function get( $route, $target, $name = null )
    {
        return $this->map('GET', $route, $target, $name);
    }
    
    /**
     * Adiciona uma rota do tipo POST
     *
     * @param  string $route
     * @param  mixed $target
     * @param  string $name
     * @return void
     */
    public function post( $route, $target, $name = null )
    {
        return $this->map('POST', $route, $target, $name);
    }
    
    /**
     * Adiciona uma rota a coleção
     *
     * @param  string $method
     * @param  string $route
     * @param  mixed $target
     * @param  string $name
     * @return Altorouter
     */
    public function map( $method, $route, $target, $name = null )
    {
        $attributes = null;
        if ( $this->hasGroupStack() ) {
            $attributes = $this->mergeWithLastGroup([]);
        }

        /** Namespace padrão */
        $namespace = '\\App\\Controllers';

        if ( isset($attributes) && is_array($attributes) ) {
            if (isset($attributes['prefix'])) {
                $route = trim($attributes['prefix'], '/').'/'.trim($route, '/');
            }

            if (!empty($name) && isset($attributes['as'])) {
                $name = trim($attributes['as']).'.'.trim($name);
            }

            if ( isset($attributes['namespace']) ) {
                $namespace = trim($attributes['namespace'], '\\');
            }
        }

        $tmpCbk['controller'] = $this->handler($target, $namespace);
        $tmpCbk['action']     = $this->action($target);
        $route                     = '/'.trim($route, '/');
        
        parent::map($method, $route, $tmpCbk, $name);
    }

    /**
     * @param $handler
     * @param $namespace
     * @return string|callable
     */
    private function handler($handler, $namespace)
    {
        return (!is_string($handler) ? $handler : "{$namespace}\\" . explode($this->separator, $handler)[0]);
    }

    /**
     * @param $handler
     * @return null|string
     */
    private function action($handler): ?string
    {
        return (!is_string($handler) ?: (explode($this->separator, $handler)[1] ?? null));
    }

    /**
     * Registra um conjunto de rotas com um conjunto de atributos compartilhados
     *
     * @param  array $attributes
     * @param  mixed $target
     * @return void
     */
    public function group( $attributes, \Closure $target )
    {
        $this->updateGroupStack($attributes);
        $target($this);
        array_pop($this->groupStack);
    }
    
    /**
     * Atualiza a pilha do grupo com os atributos fornecidos
     *
     * @param  array $attributes
     * @return void
     */
    private function updateGroupstack( $attributes )
    {
        if ( !empty($this->groupStack) ) {
            $attributes = $this->mergeWithLastGroup($attributes);
        }
        $this->groupStack[] = $attributes;
    }
    
    /**
     * Mescla os atributos de grupo fornecidos com o último grupo adicionado
     *
     * @param  array $new
     * @return array
     */
    private function mergeWithLastGroup( $new )
    {
        return $this->mergeGroup($new, end($this->groupStack));
    }
    
    /**
     * Mescla os atributos de grupo fornecidos
     *
     * @param  array $new
     * @param  array $old
     * @return array
     */
    private function mergeGroup( $new, $old )
    {
        $new['namespace'] = static::formatUsesPrefix($new, $old);
        $new['prefix']    = static::formatGroupPrefix($new, $old);

        if ( isset($old['as']) ) {
            $new['as'] = $old['as'].(isset($new['as']) ? '.'.$new['as'] : '');
        }
        return array_merge_recursive(['namespace', 'prefix', 'as'], $new);
    }

    /**
     * Formata o namespace para os novos atributos de grupo.
     *
     * @param  array $new
     * @param  array $old
     * @return string|null
     */
    private static function formatUsesPrefix( $new, $old )
    {
        if ( isset($new['namespace']) ) {
            return isset($old['namespace']) && strpos($new['namespace'], '\\') !== 0
                ? trim($old['namespace'], '\\').'\\'.trim($new['namespace'], '\\')
                : trim($new['namespace'], '\\');
        }
        return $old['namespace'] ?? null;
    }
    
    /**
     * Formata o prefixo de rota para os novos atributos de grupo.
     *
     * @param  array $new
     * @param  array $old
     * @return string|null
     */
    private static function formatGroupPrefix( $new, $old )
    {
        $oldPrefix = $old['prefix'] ?? null;

        if ( isset($new['prefix']) ) {
            return trim($oldPrefix, '/').'/'.trim($new['prefix'], '/');
        }
        return $oldPrefix;
    }
    
    /**
     * Verifica se o roteador possui atualmente uma pilha de grupo
     *
     * @return bool
     */
    public function hasGroupStack()
    {
        return !empty($this->groupStack);
    }
}