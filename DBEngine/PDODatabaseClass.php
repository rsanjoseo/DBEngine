<?php

namespace rSanjoSEO\DBEngine;

abstract class PDODatabaseClass extends DatabaseClass {
    
    protected $ctrl;

    public function connect($host, $dbname, $newdb, $user, $pass) {
        $this->database=$dbname;
        $dsn="$this->ctrl:".(isset($dbname)?"dbname=$dbname;":"")."host=$host;charset=UTF8";
        // $this->log->log('Info', "<p>Conectando [$dsn]</p>", 'Connect en PDODatabaseClass');
        try {
            $this->link = new \PDO($dsn, $user, $pass, array(\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION));
        } catch (\PDOException $e) {
            $this->lasterror=$e->getMessage();
            $this->log->log('Crítico', "Falló la conexión con $dsn. ".$e->getMessage(), __CLASS__ . ' connect');
        }
        return $this;
    }
    
    public function listTables()
    {
                return parent::listTables();

        $sql = 'SHOW TABLES';
        $query = $this->query($sql);
        return $query->fetchAll();  // PDO::FETCH_COLUMN
    }    
    
    public function escape($var){
        return $this->link->quote($var);
    }

    public function getErrorNo(){
        return $this->link->errorCode();
    }

    public function getError(){
        return $this->link->errorInfo();
    }
    
    // Ejecuta un query y retorna el restulado en su formato nativo
    protected function runQuery($qry)
    {
        return $this->link->query($qry);
    }
    
    // Pasa el resultado de un query en formato nativo a un array asociativo
    protected function fetchAll($result)
    {
        var_dump($result);
        return $result->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function select($qry)
    {
        return $this->link->query($qry);
    }
    
    public function query($qry)
    {
        return $this->link->query($qry);
    }
    
}
