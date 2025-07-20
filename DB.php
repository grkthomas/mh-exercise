<?php

// database.php

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

class DB {

    public static function getConnection() {
        // Simple SQLite setup. The 'users.db' file is created automatically.
        try {
            $pdo = new PDO('sqlite:'.__DIR__.'/users.db');
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $pdo->exec("CREATE TABLE IF NOT EXISTS users (
                id         INTEGER      PRIMARY KEY AUTOINCREMENT,
                firstname  VARCHAR(255) NOT NULL,
                lastname   VARCHAR(255) NOT NULL,
                email      VARCHAR(255) NOT NULL UNIQUE,
                country    VARCHAR(255) NOT NULL,
                phone      VARCHAR(255) NOT NULL,
                phone_code INTEGER      NOT NULL,
                experience INTEGER      NOT NULL
            )");
    
            return $pdo;
        } 
        catch( PDOException $e ) {
            $_SESSION['message'] = "Database error: ".$e->getMessage();
            $_SESSION['mtype'] = "danger";
            header("Location: register-front.php"); exit();
        }
    }
    
    public static function insertUser($data) {
        // It's important to use the prepare() and bindParam() methods instead of giving a fully prepare string to be executed. 
        // This way we ensuring protection from sql injections.
        try {
            $pdo = self::getConnection();
            $stmt = $pdo->prepare("INSERT 
                INTO users ( firstname,  lastname,  email,  country,  phone,  phone_code,  experience) 
                VALUES     (:firstname, :lastname, :email, :country, :phone, :phone_code, :experience)
            ");
            $stmt->bindParam(':firstname',  $data["firstname"]);
            $stmt->bindParam(':lastname',   $data["lastname"]);
            $stmt->bindParam(':email',      $data["email"]);
            $stmt->bindParam(':country',    $data["country"]);
            $stmt->bindParam(':phone',      $data["phone"]);
            $stmt->bindParam(':phone_code', $data["phone_code"], PDO::PARAM_INT);
            $stmt->bindParam(':experience', $data["experience"], PDO::PARAM_INT); 
    
            if( $stmt->execute() ) { return TRUE; } else { return FALSE; /* Log error */ }
        } 
        catch( PDOException $e ) {
            /* Log error */
        }

    }
}
