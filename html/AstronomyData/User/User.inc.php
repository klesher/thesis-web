<?php

const SEL_IDUSER = "SELECT idUser
                    FROM User
                    WHERE idUser=:username
                    LIMIT 1";


const INS_USER = "INSERT INTO User (idUser, password, firstName, lastName)
                  VALUES(:username, :password, :firstName, :lastName)";

class User {
    private $username = NULL;
    private $password = NULL;
    private $firstName = NULL;
    private $lastName = NULL;

/**************************************************************************************************/
/*****************************************CONSTRUCTORS*********************************************/
/**************************************************************************************************/
    public function __construct($username, $password, $firstName, $lastName) {
        $this->username = $username;
        $this->password = $password;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }

/**************************************************************************************************/
/*****************************************FACILITATORS*********************************************/
/**************************************************************************************************/
    public function hasID() {
        return !empty($this->username);
    }

/**************************************************************************************************/
/*****************************************DATABASE*************************************************/
/**************************************************************************************************/
    public function toDatabase($connection) {
        $id = $this->selectExistingID($connection);

        //No existing ID could be found - insert new.
        if (!$id)
           return $this->insertNew($connection);

        return $id;
    }

    public function insertNew($connection) {
        $stmt = $connection->prepare(INS_USER);
        $stmt->bindParam(":username", $this->username, PDO::PARAM_STR);
        $stmt->bindParam(":password", $this->password, PDO::PARAM_STR);
        $stmt->bindParam(":firstName", $this->firstName, PDO::PARAM_STR);
        $stmt->bindParam(":lastName", $this->lastName, PDO::PARAM_STR);


        if (!$stmt->execute()) {
            echo "User: " . $connection->error . "\n";
            return NULL;
        }
        return True;
    }

    public function selectExistingID($connection) {
        $stmt = $connection->prepare(SEL_IDUSER);
        $stmt->bindParam(":username", $this->username, PDO::PARAM_STR);

        if (!$stmt->execute()) {
            echo "User: " . $connection->error . "\n";
            return NULL;
        }

        return $stmt->fetchColumn();
    }

/**************************************************************************************************/
/******************************************OUTPUT**************************************************/
/**************************************************************************************************/
    public function print($depth) {
        AstronomyData::printProperty("", $this->text, $depth);
    }
}

?>
