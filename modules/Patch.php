<?php
class Patch{
    protected $pdo;

    public function __construct(\PDO $pdo) {
    $this->pdo = $pdo;
    }

    public function patchRecipes($body, $id){
        $values = [];
        $errmsg = "";
        $code = 0;

    foreach($body as $values){
        array_push($values, $values);
    }
array_push( $values, $id);
    try{
        $sqlString = "UPDATE recipe_tbl SET name = ?, description = ?, category = ?, cookingtime = ?, servings = ? WHERE id = ?";
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

    public function patchIngredients($body, $id){
        $values = [];
        $errmsg = "";
        $code = 0;

    foreach($body as $values){
        array_push($values, $values);
    }
array_push($values, $id);
    try{
        $sqlString = "UPDATE ingredients_tbl SET name = ? WHERE id = ?";
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
    public function archiveRecipes($id){
        $errmsg = "";
        $code = 0;
   
        try{
        $sqlString = "UPDATE recipe_tbl SET isdeleted = 1 WHERE id = ?";
        $sql = $this->pdo->prepare($sqlString);
        $sql ->execute([$id]);
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

    public function archiveIngredients($id){
        $errmsg = "";
        $code = 0;
   
        try{
        $sqlString = "UPDATE recipe_tbl SET isdeleted = 1 WHERE id = ?";
        $sql = $this->pdo->prepare($sqlString);
        $sql ->execute([$id]);
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