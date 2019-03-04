<?php
//TODO: Convert all entries to SI unit of meters/second to save on entries. Convert back based on user preferences.
const SEL_WIND_IDWIND = "SELECT idWind
                         FROM Wind
                         WHERE velocity=:velocity
                         AND units=:units
                         AND direction=:direction
                         LIMIT 1";
const INS_WIND = "INSERT INTO Wind
                  (velocity, units, direction)
                  VALUES(:velocity, :units, :direction)";

class Wind {
    private $velocity = NULL;
    private $units = NULL;
    private $direction = NULL;

/**************************************************************************************************/
/*****************************************CONSTRUCTORS*********************************************/
/**************************************************************************************************/
    public function __construct($simpleXML) {
        if ($simpleXML) {
            $this->velocity = (float)AstronomyData::simpleXMLToProperty($simpleXML);
            $this->units = (string)AstronomyData::simpleXMLToProperty($simpleXML->attributes()->{"Units"});
            $this->direction = (string)AstronomyData::simpleXMLToProperty($simpleXML->attributes()->{"Direction"});
        }
    }

/**************************************************************************************************/
/*****************************************FACILITATORS*********************************************/
/**************************************************************************************************/
    public function isEmpty() {
        return empty($this->velocity) || empty($this->units) || empty($this->direction);
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
    $stmt = $connection->prepare(INS_WIND);
    $stmt->bindParam(":velocity", $this->velocity, PDO::PARAM_STR);
    $stmt->bindParam(":units", $this->units, PDO::PARAM_STR);
    $stmt->bindParam(":direction", $this->direction, PDO::PARAM_STR);

    if (!$stmt->execute()) {
        echo "Wind: " . $connection->error . "\n";
        return NULL;
    }

    return $connection->lastInsertID();
}

public function selectExistingID($connection) {
    $stmt = $connection->prepare(SEL_WIND_IDWIND);
    $stmt->bindParam(":velocity", $this->value, PDO::PARAM_STR);
    $stmt->bindParam(":units", $this->units, PDO::PARAM_STR);
    $stmt->bindParam(":direction", $this->direction, PDO::PARAM_STR);

    if (!$stmt->execute()) {
        echo "Wind: " . $connection->error . "\n";
        return NULL;
    }

    return $stmt->fetchColumn();
}

/**************************************************************************************************/
/******************************************OUTPUT**************************************************/
/**************************************************************************************************/
    public function print($depth) {
        AstronomyData::printProperty("Velocity:", $this->velocity, $depth);
        AstronomyData::printProperty("Units:", $this->units, $depth);
        AstronomyData::printProperty("Direction:", $this->direction, $depth);
    }
}

?>
