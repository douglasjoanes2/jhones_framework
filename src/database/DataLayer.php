<?php

namespace App\Database;

use App\Traits\Crud;
use Exception;

class DataLayer
{
    use Crud;
    
    /**
     * @var PDO
     */
    protected $pdo;
    
    /**
     * @var string
     */
    protected $tableName;
        
    /**
     * @var array
     */
    protected $required;
        
    /**
     * @var string
     */
    protected $primaryKey;
    
    /**
     * @var bool
     */
    protected $timestamp;
    
    /**
     * @var object
     */
    protected $data;
    
    /**
     * @var string
     */
    protected $error;
    
    /**
     * __construct
     *
     * @param  string $tableName
     * @param  array $required
     * @param  string $primaryKey
     * @param  bool $timestamp
     * @return void
     */
    public function __construct( string $tableName = "", array $required = [], string $primaryKey = "id", bool $timestamp = true )
    {
        $this->pdo          = DatabaseFactory::getInstance();
        $this->tableName    = $_ENV["DB_TABLE_PREFIX"] . $tableName;
        $this->required     = $required;
        $this->primaryKey   = $primaryKey;
        $this->timestamp    = $timestamp;
    }
    
    /**
     * __wakeup
     *
     * @return void
     */
    public function __wakeup()
    {
        $this->pdo = DatabaseFactory::getInstance();
    }
    
    /**
     * __set
     *
     * @param  mixed $name
     * @param  mixed $value
     * @return void
     */
    public function __set( $name, $value )
    {
        if ( empty($this->data) ) {
            $this->data = new \stdClass();
        }
        $this->data->$name = $value;
    }
    
    /**
     * __isset
     *
     * @param  mixed $name
     * @return bool
     */
    public function __isset( $name )
    {
        return isset($this->data->$name);
    }
    
    /**
     * __get
     *
     * @param  mixed $name
     * @return mixed
     */
    public function __get( $name )
    {
        return ($this->data->$name ?? null);
    }
    
    /**
     * Retorna os dados da tabela
     *
     * @return object|null
     */
    public function data()
    {
        return $this->data;
    }
    
    /**
     * Retorna uma mensagem de erro
     *
     * @return object|null
     */
    public function error()
    {
        return $this->error;
    }
        
    /**
     * Lê todos os registros da tabela
     *
     * @return object|null
     */
    public function findAll()
    {
        $res = $this->read("SELECT * FROM %s");
        if ( !$res ) {
            return null;
        }
        return $res;
    }
    
    /**
     * Lê um registro da tabela comparando pela sua chave primária
     *
     * @param  mixed $key
     * @return object|null
     */
    public function findByPrimaryKey( $primaryKey )
    {
        $res = $this->read("SELECT * FROM %s WHERE {$this->primaryKey}=? LIMIT 1", [$primaryKey], false);
        if ( !$res ) {
            return null;
        }
        return $res;
    }
    
    /**
     * Insere ou atualiza um registro da tabela
     *
     * @return bool
     */
    public function save()
    {
        $primary    = $this->primaryKey;
        $id         = null;
        $arrayData  = $this->safe();
        $dateNow    = (new \DateTime())->format("Y-m-d H:i:s");

        try {

            // Valida os campos obrigatórios
            if ( !$this->required() ) {
                throw new Exception("Por favor, preencha os campos necessários.");
            }

            if ( empty($this->data->$primary) ) {

                // Insere um novo registro
                if ( $this->timestamp ) {
                    $arrayData["created_at"] = $dateNow;
                    $arrayData["updated_at"] = $dateNow;
                }
                $id = $this->create($arrayData);
                
            } else {

                // Atualiza um registro
                if ( $this->timestamp ) {
                    $arrayData["updated_at"] = $dateNow;
                }
                $id = $this->data->$primary;
                $this->update($arrayData, ["{$this->primaryKey}=" => $id]);
            }

            if ( !$id ) {
                return false;
            }

            $this->data = $this->findByPrimaryKey($id);
            return true;

        } catch(Exception $ex) {
            $this->error = $ex;
            return false;
        }
    }
    
    /**
     * Apaga um registro da tabela comparando pela sua chave primária
     *
     * @return bool
     */
    public function destroy()
    {
        $primary = $this->primaryKey;
        $id      = $this->data->$primary;

        if ( empty($id) ) {
            return false;
        }
        return $this->delete(["{$this->primaryKey}=" => $id]);
    }
    
    /**
     * Prepara os dados para serem gravados
     *
     * @return array
     */
    protected function safe()
    {
        $safe = (array)$this->data;
        unset( $safe[$this->primaryKey] );
        return $safe;
    }
    
    /**
     * Valida os campos obrigatórios
     *
     * @return bool
     */
    protected function required()
    {
        $data = (array)$this->data;
        foreach ($this->required as $field) {
            if ( empty($data[$field]) ) {
                return false;
            }
        }
        return true;
    }
}