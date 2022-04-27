<?php 

require_once("database.php");
require_once("security.php");

$action = secureInput($_GET["action"] ?? null);
$entity = secureInput($_GET["entity"] ?? null);
$constraints = secureInput($_GET["constraints"] ?? null);

//colonnes
$columns = secureInput($_GET["columns"] ?? null);
$columns_array = explode(",", $columns);

//values
$values = secureInput($_GET["values"] ?? null);
$values_array = unserializeValues($values);

switch ($action) {
    case 'read':
        if(!empty($entity) && !empty($columns)){
            $request = "SELECT $columns FROM $entity";
            $request .= empty($constraints)?"":" WHERE $constraints";
            $result = mysqli_query($db, $request);

            if($result === false){
                echo json_encode([  
                    "status" => REQUEST_ERROR,
                    "message" => utf8_encode("Erreur requête")
                ]);
            } else {
                echo json_encode([
                    "status" => REQUEST_SUCCESS,
                    "data" => entityToJson($entity, $columns_array, mysqli_fetch_all($result, MYSQLI_ASSOC))
                ]);
            }
        } else {
            echo json_encode([
                "status" => REQUEST_ERROR,
                "message" => utf8_encode("Impossible de lire les données")
            ]);
        }
        break;
    case 'create':
        if(!empty($entity) && (count($columns_array) == count($values_array))){
            $request = "INSERT INTO $entity ($columns) VALUES ('".implode("','", $values_array)."')";
            $request .= empty($constraints)?"":" WHERE $constraints";
            $result = mysqli_query($db, $request);

            if($result === false){
                echo json_encode([  
                    "status" => REQUEST_ERROR,
                    "message" => utf8_encode("Erreur requête")
                ]);
            } else {
                echo json_encode([
                    "status" => REQUEST_SUCCESS,
                    "message" => utf8_encode("Création réussie")
                ]);
            }
        } else {
            echo json_encode([
                "status" => REQUEST_ERROR,
                "message" => utf8_encode("Impossible de lire les données")
            ]);
        }
        break;
    
    default:
        # code...
        break;
}

