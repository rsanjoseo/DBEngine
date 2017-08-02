<?php

namespace rSanjoSEO\DBEngine;

/*******************************************************************************
 * LogFile
 * 
 * Clase que registra los mensajes que proporcionan cada aplicación.
 * 
 * El sistema es muy básico y podrá ser sustituído con facilidad por el que use
 * su aplicación.
 * 
 * @author: Rafael San José (info@rsanjoseo.com)
 * 
 */

class LogFile {

    private static $logArray;

    public function __construct()
    {
        if (!is_array(self::$logArray)) {
            self::$logArray = [];
        }
    }
    
    public function __destruct()
    {
        /*
         * Aunque la idea era usar el destructor para mostrar todos los mensajes
         * de golpe al final, resulta que cada vez que se crea una nueva variable
         * en el constructor de DatabaseController y DatabaseClass, se lanza
         * el destructor, así que de momento esto queda comentado hasta que
         * decidamos una implementación mejor y definitiva del Log, una vez que
         * lancemos la primera versión en producción.
         */
        // $this->display();
    }
    
    public function log($status, $message, $context='') {
        $item = array(
            'time' => time(),
            'status' => $status,
            'message' => $message,
            'context' => $context
        );
        self::$logArray[] = $item;
        echo '<p>(<strong>'.$item['status'].'</strong>) '.$item['message'].' <strong>en</strong> '.$item['context'].'</p>';
        
    }
    
    public function display() {
        echo '<table border="1">';
        echo '<tr><th>timestamp</th><th>status</th><th>message</th><th>context</th></td>';
        foreach (self::$logArray as $item) {
            echo '<tr>';
            echo '<td>'.date('Y-m-d h:m:s', $item['time']).'</td>';
            echo '<td>'.$item['status'].'</td>';
            echo '<td>'.$item['message'].'</td>';
            echo '<td>'.$item['context'].'</td>';
            echo '</tr>';
        }
        echo '</table>';
    }
    
}
