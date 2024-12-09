<?php

require_once "./configs/db.php";
require_once "./modules/Get.php";
require_once "./modules/Post.php";
require_once "./modules/Patch.php";


$db = new Connection();
$pdo = $db->connect();
$post = new Post($pdo);
$get = new Get($pdo);
$patch = new Patch($pdo);

if (isset($_REQUEST['request'])) {
    $request = explode("/", $_REQUEST['request']);
} else {
    echo "URL does not exist";
}


switch ($_SERVER['REQUEST_METHOD']) {

    case "GET";
        switch ($request[0]) {

            case "recipes":
                if (count($request) > 1) {
                    echo json_encode($get->getRecipes($request[1]));
                }
                else {
                    echo json_encode($get->getRecipes());
                }
                break;
            case "ingredients":
                echo json_encode($get->getIngredient());
                break;

            default:
                http_response_code(401);
                echo "this is invalid endpoint";
                break;
        }

        break;

    case "POST":
        $body = json_decode(file_get_contents("php://input"));
        switch ($request[0]) {
            case "recipes":
                echo json_encode($post->postRecipes($body));
                break;
            case "ingredients":
                echo json_encode($post->postIngredients($body));
                break;

            default:
                http_response_code(401);
                echo "this is invalid endpoint";
                break;
        }

        break;

        case "PATCH":
            $body = json_decode(file_get_contents("php://input"));
            switch ($request[0]) {
                case "recipes":
                    echo json_encode($patch->patchIngredients($body, $request[1]));
                    break;
                
                    case "ingredients":
                        echo json_encode($patch->patchRecipes($body, $request[1]));
                        break;

                        default:
                http_response_code(401);
                echo "this is invalid endpoint";
                break;
            }

            case"DELETE":
                switch ($request[0]) {
                    case"recipes":
                        echo json_encode($patch->archiveRecipes($request[1]));
                        break;

                        case"ingredients":
                            echo json_encode($patch->archiveRecipes($request[1]));
                            break;
                            default:
                            http_response_code(401);
                            echo "this is invalid endpoint";
                            break;

                }
            
            break;
            
            default:
            http_response_code(400);
            echo "Invalid HTTP method";

            break;
    }
