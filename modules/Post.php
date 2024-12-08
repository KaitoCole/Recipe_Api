<?php
class Post {
    protected $pdo;

    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function postRecipes($body) {
        $errmsg = "";
        $code = 0;

        try {
            $sqlString = "INSERT INTO recipe_tbl (recipe_name, recipe_description, recipe_category, recipe_cooking_time, recipe_servings) VALUES (?,?,?,?,?)";
            $sql = $this->pdo->prepare($sqlString);

            foreach ($body as $item) {
                $sql->execute([
                    $item['recipe_name'], 
                    $item['recipe_description'], 
                    $item['recipe_category'], 
                    $item['recipe_cooking_time'], 
                    $item['recipe_servings']
                ]);
            }

            $code = 200;
            $data = null;

            return array("code" => $code, "data" => $data);
        } catch (\PDOException $e) {
            $errmsg = $e->getMessage();
            $code = 400;
        }

        return array("errmsg" => $errmsg, "code" => $code);
    }

    public function postIngredients($body) {
        $errmsg = "";
        $code = 0;

        try {
            $sqlString = "INSERT INTO ingredients_tbl (name) VALUES (?)";
            $sql = $this->pdo->prepare($sqlString);

            foreach ($body as $item) {
                $sql->execute([$item['name']]);
            }

            $code = 200;
            $data = null;

            return array("code" => $code, "data" => $data);
        } catch (\PDOException $e) {
            $errmsg = $e->getMessage();
            $code = 400;
        }

        return array("errmsg" => $errmsg, "code" => $code);
    }
}
?>
