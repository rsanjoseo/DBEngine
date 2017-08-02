<?php

namespace rSanjoSEO\DBEngine;

/*******************************************************************************
 * DatabaseClass
 * 
 * Esta clase contiene los métodos y atributos imprescindibles para la creación
 * y gestión de una base de datos abstracta.
 * 
 * Debe de crear toda la infraestructura que nos pueda hacer falta en nuestras
 * bases de datos y forzar a que las descendientes la cumplan.
 * 
 * Inicialmente se creará una desciendiente MySQLClass y otra PDODatabaseClass,
 * si bien, es muy posible que nos baste con la 2ª, ya que hoy por hoy, casi
 * todo se puede hacer desde PDO y PHP da un soporte bastante amplio de por sí.
 * 
 * Más adelante, es bastante posible que se elimine MySQLClass y ésta clase
 * integre a PDODatabaseClass para facilitar aún más el módulo.
 * 
 * Se usa la clase LogFile, que es un sencillo sistema de logs, pero en futuras
 * versiones se podrá pasar un objeto en el constructor especificando un formato
 * estandarizado para poder usar el log que habitualmente se utilice en su
 * aplicación.
 * 
 * En un principio, y hasta ver si nos interesa crear una clase tipo registro,
 * las funcionalidades que deberían de quedar soportadas son las de la clase PDO
 * 
 * No es necesario que sean exactamente iguales, hay que centrarse en las 
 * funcionalidades.
 * 
 * PDO::__construct — Crea una instancia de PDO que representa una conexión a una base de datos 
 * 
 * PDO::quote — Entrecomilla una cadena de caracteres para usarla en una consulta
 * PDO::query — Ejecuta una sentencia SQL, devolviendo un conjunto de resultados como un objeto PDOStatement
 * PDO::prepare — Prepara una sentencia para su ejecución y devuelve un objeto sentencia
 * PDO::exec — Ejecuta una sentencia SQL y devuelve el número de filas afectadas
 * 
 * PDO::beginTransaction — Inicia una transacción
 * PDO::commit — Consigna una transacción
 * PDO::rollBack — Revierte una transacción
 * PDO::inTransaction — Comprueba si una transacción está activa
 * 
 * PDO::errorCode — Obtiene un SQLSTATE asociado con la última operación en el manejador de la base de datos
 * PDO::errorInfo — Obtiene información extendida del error asociado con la última operación del manejador de la base de datos
 * PDO::getAttribute — Devuelve un atributo de la conexión a la base de datos
 * PDO::getAvailableDrivers — Devuelve un array con los controladores de PDO disponibles
 * PDO::lastInsertId — Devuelve el ID de la última fila o secuencia insertada
 * PDO::setAttribute — Establece un atributo
 * 
 * @author: Rafael San José (info@rsanjoseo.com)
 * 
 */

// Constantes con usuario y contraseña de acceso a los DBA
define('FIREBIRD_USER', 'SYSDBA');
define('FIREBIRD_PASS', 'masterkey');
define('MYSQL_USER', 'root');
define('MYSQL_PASS', '');

abstract class DatabaseClass {
    protected $dba;         // Nombre del controlador que se está usando
    protected $database;    // El nombre de la base de datos
    protected $link;        // Conexión con la base de datos
    protected $log;
    protected $lasterror;
    
    public function __construct($dba, $dbname=null)
    {
        if (!isset(LogFile::$logArray)) $this->log=new LogFile();
        
        $this->dba=$dba;
        $this->database=$dbname;

        return $this->connect('localhost', $dbname);
    }
    
    // Conecta con el administrador de base de datos (sin definir una base de datos)
    // Si se le pasa una base de datos se intenta abrir y/o crear
    protected abstract function connect($host, $dbname=null, $newdb=false, $user, $pass);

    protected function _listTablesQry()
    {
        return 'SHOW TABLES;';
    }
    
    protected function _createDatabase($dbname)
    {
        return "CREATE DATABASE IF NOT EXISTS $dbname;";
    }
    
    //public abstract function listTables();
    public function listTables()
    {
        $res = $this->query($this->_listTablesQry());
        return $this->fetchAll($res);
        
        $tables = [];
        $aux = $this->query('SHOW TABLES;');
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
    
    public function info()
    {
        return 
            "Controlador (<strong>$this->ctrl</strong>)".
            (isset($this->database)?"Base de datos <strong>$this->database</strong>":"Sin base de datos asignada");
    }
    
    public function linked()
    {
        return isset($this->link);
    }
    
    public function getLastError()
    {
        return $this->lasterror;
    }
    
    public function createDatabase($dbname)
    {
        $cad=$this->_createDatabase($dbname);
        echo "<p>Ejecutando <code>$cad</code></p>";
        return $this->query($cad);
    }
    
    /*
    
    
    // Establece la conexión con una base de datos
    // public abstract function connectDB($host, $dbname, $user, $pass);
    
    // Ejecuta un query y retorna el restulado en su formato nativo
    protected abstract function runQuery($qry);
    
    // Pasa el resultado de un query en formato nativo a un array asociativo
    protected abstract function fetchAll($result);

    // Retorna un array asociativo con el resultado de una consulta
    public abstract function select($qry);
    
    // Ejecuta una consulta y retorna el número de filas afectadas
    public abstract function query($qry);
    
    public function isConnected()
    {
        return $this->link != null;
    }
    */
    
    /*
    private function replaceParams($coincidencias) {
        $b = current($this->params);
        next($this->params);
        return $b;
    }

    private function prepare($sql, $params) {
        for ($i = 0; $i < sizeof($params); $i++) {
            if (is_bool($params[$i])) {
                $params[$i] = $params[$i] ? 1 : 0;
            } elseif (is_double($params[$i]))
                $params[$i] = str_replace(',', '.', $params[$i]);
            elseif (is_numeric($params[$i]))
                $params[$i] = $this->escape($params[$i]);
            elseif (is_null($params[$i]))
                $params[$i] = "NULL";
            else
                $params[$i] = "'" . $this->escape($params[$i]) . "'";
        }

        $this->params = $params;
        $q = preg_replace_callback("/(\?)/i", array($this, "replaceParams"), $sql);

        return $q;
    }

    private function sendQuery($q, $params) {
        $query = $this->prepare($q, $params);
        $result = $this->query($query);
        if ($this->getErrorNo()) {
            // Controlar errores
        }
        return $result;
    }

    public function executeScalar($q, $params = null) {
        $result = $this->sendQuery($q, $params);
        if (!is_null($result)) {
            if (!is_object($result)) {
                return $result;
            } else {
                $row = $this->fetchArray($result);
                return $row[0];
            }
        }
        return null;
    }

    public function execute($q, $params = null) {
        $result = $this->sendQuery($q, $params);
        if (is_object($result)) {
            $arr = array();
            while ($row = $this->fetchArray($result)) {
                $arr[] = $row;
            }
            return $arr;
        }
        return null;
    }
    */
}
