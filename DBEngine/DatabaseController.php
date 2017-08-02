<?php

namespace rSanjoSEO\DBEngine;

/*******************************************************************************
 * DatabaseController
 * 
 * Esta clase es la que realiza la conexión con las bases de datos usando el 
 * desdenciente de DatabaseClass que corresponda según el controlador que
 * queramos utilizar.
 * 
 * Está construída para poder utilizar más de un controlador y base de datos
 * simultáneamente, de manera que se pueda facilitar la importación/exportación
 * de datos, así como la interacción entre distintas aplicaciones.
 * 
 * El constructor es privado para evitar la tentación de ser invocado desde
 * fuera de la clase. Para crear una conexión con una base de datos usaremos
 * el método getConnection, que devolverá la conexión al controlador y base de
 * datos solicitados, creándolo si no existe.
 * 
 * A estas alturas de la película no tengo claro de si esta clase debe de hacer
 * algo más que cargar y gestionar las clases DatabaseClass a usar o si debe
 * de tener métodos que hagan algo más.
 * 
 * ¿getConnection debería de retornar un tipo DataController o DatabaseClass?
 * En el primer caso, toda la gestión tendría que repetirse en DataController,
 * si bien es cierto que se podría suavizar bastante la gestión. En el segundo
 * caso, DataController delegaría todo el trabajo en DatabaseClass y creo que
 * el resultado final sería mucho más sencillo y eficiente. Sin tener claro qué
 * es mejoras introduciría la diferenciación, creo que lo mejor sería retornar
 * un tipo DatabaseClass.
 * 
 * @author: Rafael San José (info@rsanjoseo.com)
 * 
 */

class DatabaseController {
    private static $connection;
    private static $database;
    private $dbcontroller;
    private $dbname;
    protected $log;

    /*
     * El constructor de DatabaseController es private para que sólo pueda ser
     * llamado desde la propia clase, ya que sólo se va a crear uno por cada
     * controlador y se hará desde... 
     * 
     * DBAConnect - Establece conexión con un controlador (sin abrir BBDD)
     * newDatabase - Abre una base de datos (si no existe, la crea)
     * getDatabaseConnection - Abre una base de datos existente
     * 
     * Revisar: DBAConnect y newDatabase no va con PDOFirebird, sí con PDOMySQL
     * 
     */
    private function __construct($_dbcontroller, $dbname=null, $newdb=false) 
    {
        if (!isset(LogFile::$logArray)) $this->log=new LogFile();
        
        $dbcontroller="rSanjoSEO\DBEngine\\$_dbcontroller";
        if (!class_exists($dbcontroller)) {
            die("$dbcontroller no encontrado.");
        }
        
        $this->dbcontroller=$_dbcontroller;
        $this->dbname=$dbname;

        if (isset($dbname))
        {   // Conexión con controlador y base de datos especificada
            // $this->log->log('Info', "Conexión con $_dbcontroller - $dbname", __CLASS__ . ' constructor');
            if (!isset(self::$database[$this->dbcontroller][$this->dbname]))
            {
                //echo "Nueva conexión $dbcontroller";
                self::$database[$this->dbcontroller][$this->dbname]=new $dbcontroller($_dbcontroller, $dbname, $newdb);
            }
        }
        else
        {   // Conexión sólo con controlador (no se ha especificado base de datos)
            // $this->log->log('Info', "Conexión con $_dbcontroller (sin base de datos)", __CLASS__ . ' constructor');
            if (!isset(self::$connection[$this->dbcontroller]))
            {
                //echo "Nueva conexión $dbcontroller";
                self::$connection[$this->dbcontroller] = new $dbcontroller($_dbcontroller);
            }
        }
    }
    
    private function createDatabase($dbcontroller, $dbname)
    {
        $con=self::$connection[$dbcontroller];
        $con->createDatabase($dbname);
    }
    
    public static function DBAConnect($dbcontroller) {
        if (!isset(self::$connection[$dbcontroller])) {
            $class = __CLASS__;
            new $class($dbcontroller);
        }
        return self::$connection[$dbcontroller];
    }
    
    public static function newDatabase($dbcontroller, $dbname) {
        self::DBAConnect($dbcontroller);
        self::createDatabase($dbcontroller, $dbname);
        if (!isset(self::$database[$dbcontroller][$dbname])) {
            $class = __CLASS__;
            new $class($dbcontroller, $dbname, true);
        }
        return self::$database[$dbcontroller][$dbname];
    }

    public static function getDatabaseConnection($dbcontroller, $dbname) {
        self::DBAConnect($dbcontroller);
        if (!isset(self::$database[$dbcontroller][$dbname])) {
            $class = __CLASS__;
            new $class($dbcontroller, $dbname);
        }
        return self::$database[$dbcontroller][$dbname];
    }
    
    public static function listControllers()
    {
        echo "<h4>Listado de controladores</h4>";
        if (isset(self::$connection))
        {
            foreach (self::$connection as $key=>$con) {
                echo "<p>$key</p>";
            }
        } else {
            echo "<p>No hay conexiones en este momento</p>";
        }
    }
    
    public static function listDatabases()
    {
        echo "<h4>Listado de bases de datos abiertas</h4>";
        if (isset(self::$connection))
        {
            foreach (self::$connection as $key=>$con) {
                echo "<p>Conexión: $key</p>";
                if (isset(self::$database) && isset(self::$database[$key]))
                {
                    foreach (self::$database[$key] as $key2=>$db) {
                        echo "<p>$key2</p>";
                    }
                } else {
                    echo "<p>No hay base de datos abiertas para $key</p>";
                }
            }
        } else {
            echo "<p>No hay conexiones en este momento</p>";
        }
    }
    
}
