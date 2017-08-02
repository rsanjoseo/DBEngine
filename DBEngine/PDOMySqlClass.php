<?php

namespace rSanjoSEO\DBEngine;

//use FacturaScripts\Core\Base\DBEngine\PDODatabaseClass;

class PDOMySqlClass extends PDODatabaseClass {
    
    public function connect($host, $dbname=null, $newdb=false, $user=MYSQL_USER, $pass=MYSQL_PASS) {
        $this->ctrl='mysql';
        return parent::connect($host, $dbname, $newdb, $user, $pass);
    }

}