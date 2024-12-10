<?php
class Authentication {

    protected $pdo;

    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function passchecker($existingHash, $inputpassword) {
        $hash = crypt($inputpassword, $existingHash);
        return $hash == $existingHash;
    }

    public function encriptor($password) {
        $hashFormat = "$2y$10$";
        $saltLength = 22;
        $salt = $this->generateSalt($saltLength);
        return crypt($password, $hashFormat . $salt);
    }

    public function generateSalt($length) {
        $urs = md5(uniqid(mt_rand(), true));
        $base64String = base64_encode($urs);
        $mb64String = str_replace("+", ".", $base64String);
        return substr($mb64String, 0, $length);
    }

    public function login($body) {
        $username = $body->username;
        $password = $body->password;
    
        $code = 0;
        $payload = "";
        $remarks = "";
        $message = "";
    
        try {
            $sqlString = "SELECT recipeid, username, password, token FROM accounts_tbl WHERE username=?";
            $stmt = $this->pdo->prepare($sqlString);
            $stmt->execute([$username]);
    
            $result = $stmt->fetchAll();
    
            if (count($result) > 0) {
                $result = $result[0];
    
                if ($this->passchecker($result['password'], $password)) {
                    $code = 200;
                    $remarks = "success";
                    $message = "Logged in successfully";
                    $payload = array(
                        "id" => $result['recipeid'],
                        "username" => $result['username'],
                        "token" => $result['token']
                    );
                } else {
                    $code = 401;
                    $remarks = "failed";
                    $message = "Incorrect Password";
                    $payload = null;
                }
            } else {
                $code = 401;
                $remarks = "failed";
                $message = "User not found";
                $payload = null;
            }
        } catch (\PDOException $e) {
            error_log($e->getMessage());
            $remarks = "failed";
            $code = 400;
            $message = "Database error. Please try again later.";
        }
    
        return array("payload" => $payload, "remarks" => $remarks, "message" => $message, "code" => $code);
    }
    

    public function addAcc($body) {
        $values = [];
        $errmsg = "";
        $code = 0;

        $body->password = $this->encriptor($body->password);

        foreach ($body as $value) {
            array_push($values, $value);
        }

        try {
            $sqlString = "INSERT INTO accounts_tbl (recipeid, username, password) VALUES (?,?,?)";
            $sql = $this->pdo->prepare($sqlString);
            $sql->execute($values);

            $code = 200;
            $data = null;
            $message = "Data successfully added";

            return array("data" => $data, "code" => $code, "message" => $message);
        } catch (\PDOException $e) {
            $errmsg = $e->getMessage();
            $code = 400;
        }

        return array("errmsg" => $errmsg, "code" => $code);
    }
}
?>
