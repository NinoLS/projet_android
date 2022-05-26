<?php 

require_once("database.php");
require_once("security.php");

//clé secondaires
$foreign_keys = ["livreID"];

$action = secureInput($_GET["action"] ?? null);
$entity = secureInput($_GET["entity"] ?? null);
$constraints = secureInput($_GET["constraints"] ?? null);

//colonnes
$columns = secureInput($_GET["columns"] ?? null);
$columns_array = explode(",", $columns);

//values
$values = secureInput($_GET["values"] ?? null);
$values_array = unserializeValues($values);

//id
$id = intval(secureInput($_GET["id"] ?? -1));

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
                    "data" => entityToJson($columns_array, mysqli_fetch_all($result))
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
            //clé étrangère
            if(!empty(array_intersect($foreign_keys, $columns_array))){
                $position = array_search("livreID", $columns_array);
                $values_array[$position]--;
                $real_id = mysqli_fetch_row(mysqli_query($db, "SELECT id FROM livre LIMIT 1 OFFSET {$values_array[$position]}"))[0];
                $values_array[$position] = $real_id;
            }

            $request = "INSERT INTO $entity ($columns) VALUES ('".implode("','", $values_array)."')";
            $request .= empty($constraints)?"":" WHERE $constraints";
            var_dump($request);die();
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
    case 'update':
        if(!empty($entity) && (count($columns_array) == count($values_array)) && $id >= 0){
            $request = "UPDATE $entity SET ";
            $fields = [];
            for ($c=0; $c < count($columns_array); $c++) { 
                $fields[] = "{$columns_array[$c]} = '{$values_array[$c]}'";
            }
            $request .= implode(", ", $fields);
            $request .= " WHERE id = $id";
            $request .= empty($constraints)?"":" AND $constraints";
            $result = mysqli_query($db, $request);

            if($result === false){
                echo json_encode([  
                    "status" => REQUEST_ERROR,
                    "message" => utf8_encode("Erreur requête")
                ]);
            } else {
                echo json_encode([
                    "status" => REQUEST_SUCCESS,
                    "message" => utf8_encode("Mise à jour réussie")
                ]);
            }
        } else {
            echo json_encode([
                "status" => REQUEST_ERROR,
                "message" => utf8_encode("Impossible de lire les données")
            ]);
        }
        break;
    case 'delete':
        if(!empty($entity) && $id >= 0){
            $request = "DELETE FROM $entity WHERE id = $id";
            $request .= empty($constraints)?"":" AND $constraints";
            $result = mysqli_query($db, $request);

            if($result === false){
                echo json_encode([  
                    "status" => REQUEST_ERROR,
                    "message" => utf8_encode("Erreur requête")
                ]);
            } else {
                echo json_encode([
                    "status" => REQUEST_SUCCESS,
                    "message" => utf8_encode(mysqli_affected_rows($db) . " suppression(s) réussie(s)")
                ]);
            }
        } else {
            echo json_encode([
                "status" => REQUEST_ERROR,
                "message" => utf8_encode("Impossible de lire les données")
            ]);
        }
        break;
}

