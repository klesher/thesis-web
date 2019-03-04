<?php

const SEL_SIZE_ID = "SELECT idSize
                     FROM Size
                     WHERE value=':value'
                     AND units=':units'
                     LIMIT 1";
const INS_SIZE = "INSERT INTO Size (value, units)
                  VALUES(:value, :units)";

class Size {
    private $value = NULL;
    private $units = NULL;

/**************************************************************************************************/
/*****************************************CONSTRUCTORS*********************************************/
/**************************************************************************************************/
    public function __construct($simpleXML) {
        if ($simpleXML) {
            $this->value = (float)AstronomyData::simpleXMLToProperty($simpleXML->Size);
            $this->units = (string)AstronomyData::simpleXMLToProperty($simpleXML->Size->attributes()->{"Units"});
        }
    }

/**************************************************************************************************/
/*****************************************FACILITATORS*********************************************/
/**************************************************************************************************/
    public function isEmpty() {
        return  empty($this->value) || empty($this->units);
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
        $stmt = $connection->prepare(INS_SIZE);
        $stmt->bindParam(":value", $this->value, PDO::PARAM_STR);
        $stmt->bindParam(":units", $this->units, PDO::PARAM_STR);

        if (!$stmt->execute()) {
            echo "Size: " . $connection->error . "\n";
            return NULL;
        }

        return $connection->lastInsertID();
    }

    public function selectExistingID($connection) {
        $stmt = $connection->prepare(SEL_SIZE_ID);
        $stmt->bindParam(":value", $this->value, PDO::PARAM_STR);
        $stmt->bindParam(":units", $this->units, PDO::PARAM_STR);

        if (!$stmt->execute()) {
            echo "Size: " . $connection->error . "\n";
            return NULL;
        }

        return $stmt->fetchColumn();
    }


/**************************************************************************************************/
/******************************************OUTPUT**************************************************/
/**************************************************************************************************/
    public function print($depth) {
        AstronomyData::printHeader("Size", $depth);
        AstronomyData::printProperty("Value:", $this->value, $depth+1);
        AstronomyData::printProperty("Units:", $this->units, $depth+1);
    }
}

?>
