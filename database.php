<?php

require_once("security.php");

// database
$db = @mysqli_connect(
    DB_SERVER,
    DB_USERNAME,
    DB_PASSWORD,
    DB_NAME,
    DB_PORT
);

//si erreur: message
if($db == false){
    echo json_encode([
        "status" => "error",
        "message" => "Impossible de se connecter à la base"
    ]);
    die();
}

