<?php

include_once "Common.php";
class Post extends Common{
    protected $pdo;

    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function postRecipes($body){
        $result = $this->postData("recipe_tbl", $body, $this->pdo);
        if($result['code'] == 200){
          $this->logger("KaitoCole", "POST", "Created a new recipe");
          return $this->generateResponse($result['data'], "success", "Successfully created a recipe.", $result['code']);
        }
        $this->logger("KaitoCole", "POST", $result['errmsg']);
        return $this->generateResponse(null, "failed", $result['errmsg'], $result['code']);
      }
  
      public function postIngredients($body){
         $result = $this->postData("ingredients_tbl", $body, $this->pdo);
         if($result['code'] == 200){
          $this->logger("KaitoCole", "POST", "Created a new Ingredient record");
          return $this->generateResponse($result['data'], "success", "Successfully created a Ingredient record.", $result['code']);
        }
        return $this->generateResponse(null, "failed", $result['errmsg'], $result['code']);
      }
  }
  
?>