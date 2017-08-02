<?php

namespace rSanjoSEO\DBEngine;

use mysqli;

class MySqlClass extends DatabaseClass {

    public function connect($host, $dbname=null, $newdb=false, $user=MYSQL_USER, $pass=MYSQL_PASS)
    {
        $this->ctrl='mysqli';
        if (isset($dbname))
        {
            $this->database=$dbname;
            if ($newdb)
            {
                $this->link = new mysqli($host, $user, $pass);
                $this->link->query("CREATE DATABASE IF NOT EXISTS $dbname;");
                $this->link->close();
            }
            $this->link = new mysqli($host, $user, $pass, $dbname);
        }
        else
        {
            $this->link = new mysqli($host, $user, $pass);
        }
        return $this;
    }
    
    /*
    public function connect($host, $dbname, $user=MYSQL_USER, $pass=MYSQL_PASS) {
        $this->ctrl='mysqli';
        $this->database=$dbname;
        $this->link = new mysqli($host, $user, $pass, $dbname);
        return $this;
    }
    */
    
/*    
PDO::beginTransaction — Inicia una transacción
PDO::commit — Consigna una transacción
PDO::__construct — Crea una instancia de PDO que representa una conexión a una base de datos
PDO::errorCode — Obtiene un SQLSTATE asociado con la última operación en el manejador de la base de datos
PDO::errorInfo — Obtiene información extendida del error asociado con la última operación del manejador de la base de datos
PDO::exec — Ejecuta una sentencia SQL y devuelve el número de filas afectadas
PDO::getAttribute — Devuelve un atributo de la conexión a la base de datos
PDO::getAvailableDrivers — Devuelve un array con los controladores de PDO disponibles
PDO::inTransaction — Comprueba si una transacción está activa
PDO::lastInsertId — Devuelve el ID de la última fila o secuencia insertada
PDO::prepare — Prepara una sentencia para su ejecución y devuelve un objeto sentencia
PDO::query — Ejecuta una sentencia SQL, devolviendo un conjunto de resultados como un objeto PDOStatement
PDO::quote — Entrecomilla una cadena de caracteres para usarla en una consulta
PDO::rollBack — Revierte una transacción
PDO::setAttribute — Establece un atributo    
 * 
 */

    public function getErrorNo() {
        return mysqli_errno($this->link);
    }

    public function getError() {
        return mysqli_error($this->link);
    }

    /*
    public function listTables() {
        $tables = [];
        $aux = $this->select('SHOW TABLES;');
        if (!empty($aux)) {
            foreach ($aux as $a) {
                $key = 'Tables_in_' . FS_DB_NAME;
                if (isset($a[$key])) {
                    $tables[] = $a[$key];
                }
            }
        }
        return $tables;
    }
    */

    // Ejecuta un query y retorna el restulado en su formato nativo
    protected function runQuery($qry)
    {
        return mysqli_query($this->link, $qry);
    }
    
    // Pasa el resultado de un query en formato nativo a un array asociativo
    protected function fetchAll($result)
    {
        $res = [];
        if ($result)
            while ($row = $result->fetch_array(MYSQLI_ASSOC)) {
                $res[] = $row;
            }
        return $res;
    }


    public function listTables()
    {
        return parent::listTables();
        $sql="
            SELECT * FROM INFORMATION_SCHEMA.TABLES
            WHERE TABLE_SCHEMA='$this->database'
        ";
        $qry=$this->query($sql);
        return $qry;
    }

    public function select($qry) {
        return mysqli_query($this->link, $qry);
    }

    public function query($qry) {
        return $this->link->query($qry);
    }

    public function fetchArray($result) {
        return mysqli_fetch_array($result);
    }

    public function isConnected() {
        return !is_null($this->link);
    }

    public function escape($var) {
        return mysqli_real_escape_string($this->link, $var);
    }
    /*
    public function select($sql)
    {
        $result = [];
        try {
            $aux = $this->query($sql);
            if ($aux) {
                $result = [];
                while ($row = $aux->fetch_array(MYSQLI_ASSOC)) {
                    $result[] = $row;
                }
                $aux->free();
            }
        } catch (Exception $e) {
            $this->lastErrorMsg = $e->getMessage();
            $result = [];
        }

        return $result;
    }
     * 
     */

}