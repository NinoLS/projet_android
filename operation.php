<?php 

require_once("database.php");
require_once("security.php");

$action = secureInput($_GET["action"] ?? "read");
$entity = secureInput($_GET["entity"] ?? null);
$constraints = secureInput($_GET["constraints"] ?? null);
$columns = secureInput($_GET["columns"] ?? null);
$columns_array = explode(",", $columns);

switch ($action) {
    case 'read':
        if(!empty($entity) && !empty($columns)){
            $request = "SELECT $columns FROM $entity";
            $request .= empty($constraints)?"":" WHERE $constraints";
            $result = mysqli_query($db, $request);

            if($result === false){
                echo json_encode([  
                    "status" => REQUEST_ERROR,
                    "message" => "Erreur requête"
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
                "message" => "Impossible de lire les données"
            ]);
        }
        break;
    
    default:
        # code...
        break;
}

