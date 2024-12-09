<?php
class Post{
    protected $pdo;

    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function postRecipes($body){
        $values = [];
        $errmsg = "";
        $code = 0;

    foreach($body as $value){
        array_push($values, $value);
    }

    try{
        $sqlString = "INSERT INTO recipe_tbl (recipe_name, recipe_description, recipe_category, recipe_cooking_time, recipe_servings) VALUES (?,?,?,?,?)";
        $sql = $this->pdo->prepare($sqlString);
        $sql ->execute($values);
        $code = 200;
        $message = "data succesfully added";
        $data = null;

        return array("code" => $code,"message" => $message, "data" => $data);
    }
        catch (\PDOException $e) { 
            $errmsg = $e->getMessage();
            $code = 400;
        }     
        
        return array("errmsg" => $code,"code"=> $code);
    }


    public function postIngredients($body){
        $values = [];
        $errmsg = "";
        $code = 0;

    foreach($body as $values){
        array_push($values, $values);
    }
     try{
        $sqlString = "INSERT INTO ingredients_tbl (ingredients_name) VALUES (?)";
        $sql = $this->pdo->prepare($sqlString);
        $sql ->execute($values);
        $code = 200;
        $data = null;

        return array("code" => $code, "data" => $data);
    }
        catch (\PDOException $e) { 
            $errmsg = $e->getMessage();
            $code = 400;
        }     
        
        return array("errmsg" => $code,"code"=> $code);
    }

}
?>