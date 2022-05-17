<?php

define("DB_SERVER", "localhost");
define("DB_USERNAME", "root");
define("DB_PASSWORD", "");
define("DB_NAME", "projet_java");
define("DB_PORT", 3306);

define("REQUEST_SUCCESS", 0);
define("REQUEST_ERROR", 1);

function secureInput($input){
    global $db;
    return trim(htmlspecialchars(mysqli_real_escape_string($db, $input)));
}

function entityToJson($columns, $data){
    $json_entities = [];
    foreach ($data as $e) {
        $tmp = [];
        for ($c=0; $c < count($e); $c++) { 
            $tmp[$columns[$c]] = $e[$c];
        }
        $json_entities[] = $tmp;
    }
    return $json_entities;
}

function unserializeValues($values){  
    //crochets début & fin
    $values = substr($values, 1, strlen($values)-2);

    //séparer les valeurs ("|")
    $v = explode("|", $values);
    
    //retour
    return $v;
}

?>