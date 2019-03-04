<?php

const SEL_NOTE_IDNOTE = "SELECT idNote
                         FROM Note
                         WHERE text=:text
                         LIMIT 1";
const INS_NOTE = "INSERT INTO Note (text)
                  VALUES(:text)";

class Note {
    private $text = NULL;

/**************************************************************************************************/
/*****************************************CONSTRUCTORS*********************************************/
/**************************************************************************************************/
    public function __construct($simpleXML) {
        $this->text = (string)AstronomyData::simpleXMLToProperty($simpleXML);
    }

/**************************************************************************************************/
/*****************************************FACILITATORS*********************************************/
/**************************************************************************************************/
    public function isEmpty() {
        return empty($this->text);
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
        $stmt = $connection->prepare(INS_NOTE);
        $stmt->bindParam(":text", $this->text, PDO::PARAM_STR);

        if (!$stmt->execute()) {
            echo "Note: " . $connection->error . "\n";
            return NULL;
        }

        return $connection->lastInsertID();
    }

    public function selectExistingID($connection) {
        $stmt = $connection->prepare(SEL_NOTE_IDNOTE);
        $stmt->bindParam(":text", $this->text, PDO::PARAM_STR);

        if (!$stmt->execute()) {
            echo "Note: " . $connection->error . "\n";
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
