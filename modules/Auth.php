<?php
class Authentication {

    protected $pdo;

    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function isAuthorized() {
        $headers = array_change_key_case(getallheaders(), CASE_LOWER);
        return $this->getToken() === $headers['authorization'];
    }

    private function getToken() {
        $headers = array_change_key_case(getallheaders(), CASE_LOWER);
        $sqlString = "SELECT token FROM accounts_tbl WHERE username=?";
        try {
            $stmt = $this->pdo->prepare($sqlString);
            $stmt->execute([$headers['x-auth-user']]);
            $result = $stmt->fetchAll()[0];
            return $result['token'];
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        return "";
    }

    private function generateHeader() {
        $header = [
            "typ" => "JWT",
            "alg" => "HS256",
            "app" => "recipe_api",
            "dev" => "Monkey Co"
        ];
        return base64_encode(json_encode($header));
    }

    private function generatePayload($user_id, $username) {
        $payload = [
            "uid" => $user_id,
            "uc" => $username,
            "by" => "Alberto and Herrera",
            "email" => "kianchasetrent@gmail.com",
            "date" => date("Y-m-d H:i:s"),
            "exp" => date("Y-m-d H:i:s")
        ];
        return base64_encode(json_encode($payload));
    }

    private function generateToken($user_id, $username) {
        $header = $this->generateHeader();
        $payload = $this->generatePayload($user_id, $username);
        $signature = hash_hmac("sha256", "$header.$payload", TOKEN_KEY);
        return "$header.$payload." . base64_encode($signature);
    }

    private function isSamePassword($inputPassword, $existingHash) {
        $hash = crypt($inputPassword, $existingHash);
        return $hash === $existingHash;
    }

    private function encryptPassword($password) {
        $hashFormat = "$2y$10$";
        $saltLength = 22;
        $salt = $this->generateSalt($saltLength);
        return crypt($password, $hashFormat . $salt);
    }

    private function generateSalt($length) {
        $urs = md5(uniqid(mt_rand(), true));
        $b64String = base64_encode($urs);
        $mb64String = str_replace("+", ".", $b64String);
        return substr($mb64String, 0, $length);
    }

    public function saveToken($token, $username){
        
        $errmsg = "";
        $code = 0;
        
        try{
            $sqlString = "UPDATE accounts_tbl SET token=? WHERE username = ?";
            $sql = $this->pdo->prepare($sqlString);
            $sql->execute( [$token, $username] );

            $code = 200;
            $data = null;

            return array("data"=>$data, "code"=>$code);
        }
        catch(\PDOException $e){
            $errmsg = $e->getMessage();
            $code = 400;
        }

        
        return array("errmsg"=>$errmsg, "code"=>$code);

    }


    public function login($body) {
        $username = $body->username;
        $password = $body->password;
    
        $code = 401;
        $payload = null;
        $remarks = "failed";
        $message = "User not found";
    
        try {
            $sqlString = "SELECT recipeid, username, password, token FROM accounts_tbl WHERE username=?";
            $stmt = $this->pdo->prepare($sqlString);
    
            if ($stmt) {
                $stmt->execute([$username]);
                $result = $stmt->fetch();
    
                if ($result) {
                    if ($this->encryptPassword($result['password'], $password)) {
                        $code = 200;
                        $remarks = "success";
                        $message = "Logged in successfully";
                        $payload = [
                            "id" => $result['recipeid'],
                            "username" => $result['username'],
                            "token" => $result['token']
                        ];
                    } else {
                        $message = "Incorrect Password.";
                    }
                }
            }
        } catch (\PDOException $e) {
            error_log("SQL Error: " . $e->getMessage());
            return ["errmsg" => $e->getMessage(), "code" => 400];
        }
    
        return ["payload" => $payload, "remarks" => $remarks, "message" => $message, "code" => $code];
    }
    

    public function addAcc($body) {
        $values = [];
        $errmsg = "";
        $code = 0;

        $body->password = $this->encryptPassword($body->password);

        foreach ($body as $value) {
            array_push($values, $value);
        }

        try {
            $sqlString = "INSERT INTO accounts_tbl (recipeid, username, password) VALUES (?,?,?)";
            $sql = $this->pdo->prepare($sqlString);
            $sql->execute($values);

            $code = 200;
            $data = null;

            return ["data" => $data, "code" => $code];
        } catch (\PDOException $e) {
            $errmsg = $e->getMessage();
            $code = 400;
        }

        return ["errmsg" => $errmsg, "code" => $code];
    }
}
?>
