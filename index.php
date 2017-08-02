<?php

/*
 * DBEngine pretende ser un gestor de bases de datos de uso libre que facilite
 * la conexión de aplicaciones PHP con bases de datos de distinto tipo desde
 * un único y fácil interfase.
 * 
 * La idea surgió para ser aplicado en Facturascripts por parte de algunos
 * desarrolladores que queríamos incorporar la opción de PDO para un mayor
 * control de las bases de datos, y añadir la posibilidad de otros motores como
 * Firebird o DB2.
 * 
 * Aún está en fase de desarrollo y le queda mucho camino por andar, por lo que
 * se agradece cualquier colaboración en el proyecto.
 * 
 * @author: Rafael San José (info@rsanjoseo.com)
 * 
 */

require_once __DIR__ . '/DBEngine/LogFile.php';
require_once __DIR__ . '/DBEngine/DatabaseClass.php';
require_once __DIR__ . '/DBEngine/DatabaseController.php';
require_once __DIR__ . '/DBEngine/MySqlClass.php';
require_once __DIR__ . '/DBEngine/PDODatabaseClass.php';
require_once __DIR__ . '/DBEngine/PDOMySqlClass.php';
require_once __DIR__ . '/DBEngine/PDOFirebirdClass.php';

$log = new rSanjoSEO\DBEngine\LogFile();

// $db = rSanjoSEO\DBEngine\DatabaseController::DBAConnect('MySqlClass');
// $db = rSanjoSEO\DBEngine\DatabaseController::DBAConnect('PDOFirebirdClass'); // NO VA
// $db = rSanjoSEO\DBEngine\DatabaseController::DBAConnect('PDOMySqlClass');
// $db = rSanjoSEO\DBEngine\DatabaseController::getDatabaseConnection('MySqlClass', 'facturascripts');
// $db = rSanjoSEO\DBEngine\DatabaseController::newDatabase('PDOFirebird', 'C:\database\NUEVA.FDB'); // NO VA
$db = rSanjoSEO\DBEngine\DatabaseController::newDatabase('PDOMySqlClass', 'Facturascripts');

rSanjoSEO\DBEngine\DatabaseController::listControllers();   // Mostraría todos los controladores cargados (sean o no Firebird)
rSanjoSEO\DBEngine\DatabaseController::listDataBases();     // Mostraría todas las bases de datos del controlador actual (Firebird)

print_r($db->listTables());

// $log->display();