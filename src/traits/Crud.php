<?php

namespace App\Traits;

use PDOException;

trait Crud
{
        
    /**
     * Ler registros da tabela
     *
     * @param  string $sql
     * @param  array $arrayParams
     * @param  bool $fetchAll
     * @return object|null
     */
    public function read( string $sql, array $arrayParams = null, bool $fetchAll = true  )
    {
        try {

            $stm = $this->pdo->prepare(sprintf($sql, $this->tableName));
            if ( !empty($arrayParams) ) {
                $count = 1;
                foreach ($arrayParams as $v) {
                    $stm->bindValue($count, $v);
                    $count++;
                }
            }
            $stm->execute();
            return (!$fetchAll ? $stm->fetchObject(static::class) : $stm->fetchAll(\PDO::FETCH_CLASS, static::class));

        } catch(PDOException $exception) {
            $this->error = $exception;
            return null;
        }
    }

    public function create( array $arrayData )
    {
        try {

            foreach ($arrayData as $k => $v) {
                $fields[] = $k;
                $bindValues[] = "?";
            }
    
            $count = 1;
            $fields = implode(", ", $fields);
            $bindValues = implode(", ", $bindValues);
    
            $stm = $this->pdo->prepare("INSERT INTO {$this->tableName} ({$fields}) VALUES ({$bindValues})");
            foreach ($arrayData as $v) {
                $stm->bindValue($count, $v);
                $count++;
            }
            
            $stm->execute();
            return $this->pdo->lastInsertId();

        } catch(PDOException $exception) {
            $this->error = $exception;
            return null;
        }
    }

    public function update( array $arrayData, array $arrayCondition )
    {
        try {

            foreach ($arrayData as $k => $v) {
                $news[] = $k;
            }
    
            foreach ($arrayCondition as $k => $v) {
                $conds[] = $k;
            }
    
            $count = 1;
            $news = implode("=?, ", $news) . "=?";
            $conds = implode("? AND ", $conds) . "?";
    
            $stm = $this->pdo->prepare("UPDATE {$this->tableName} SET {$news} WHERE {$conds}");
            
            foreach ($arrayData as $v) {
                $stm->bindValue($count, $v);
                $count++;
            }
    
            foreach ($arrayCondition as $v) {
                $stm->bindValue($count, $v);
                $count++;
            }
    
            $stm->execute();
            return ($stm->rowCount() ?? 1);

        } catch(PDOException $exception) {
            $this->error = $exception;
            return null;
        }
    }

    public function delete( array $arrayCondition )
    {
        try {

            foreach ($arrayCondition as $k => $v) {
                $conds[] = $k;
            }
    
            $count = 1;
            $conds = implode("? AND ", $conds) . "?";
    
            $stm = $this->pdo->prepare("DELETE FROM {$this->tableName} WHERE {$conds}");
            foreach ($arrayCondition as $v) {
                $stm->bindValue($count, $v);
                $count++;
            }
            $stm->execute();
            return true;

        } catch(PDOException $exception) {
            $this->error = $exception;
            return false;
        }
    }
}