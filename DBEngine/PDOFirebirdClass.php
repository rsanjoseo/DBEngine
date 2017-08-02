<?php

namespace rSanjoSEO\DBEngine;

//use FacturaScripts\Core\Base\DBEngine\PDODatabaseClass;

class PDOFirebirdClass extends PDODatabaseClass {
    
    protected function _listTablesQry()
    {
        return '
            select rdb$relation_name
            from rdb$relations
            where rdb$view_blr is null 
            and (rdb$system_flag is null or rdb$system_flag = 0);';
    }
    
    public function connect($host, $dbname=null, $newdb=false, $user=FIREBIRD_USER, $pass=FIREBIRD_PASS) {
        $this->ctrl='firebird';
        $res=parent::connect($host, $dbname, $newdb, $user, $pass);
        return $res;
    }

}