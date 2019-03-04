<?php
//TODO: Convert all entries to SI unit Kelvin to save on entries. Convert back based on user preferences.

const SEL_TEMPERATURE_IDTEMPERATURE = "SELECT idTemperature
                                       FROM Temperature
                                       WHERE value=:value
                                       AND units=:units
                                       LIMIT 1";
const INS_TEMPERATURE = "INSERT INTO Temperature
                         (value, units)
                         VALUES(:value, :units)";

class Temperature {
    private $value = NULL;
    private $units = NULL;

/**************************************************************************************************/
/*****************************************CONSTRUCTORS*********************************************/
/**************************************************************************************************/
    public function __construct($simpleXML) {
        $this->value = (float)AstronomyData::simpleXMLToProperty($simpleXML);
        $this->units = (string)AstronomyData::simpleXMLToProperty($simpleXML->attributes()->{"Units"});
    }

/**************************************************************************************************/
/*****************************************FACILITATORS*********************************************/
/**************************************************************************************************/
    public function isEmpty() {
        return empty($this->value) || empty($this->units);
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
    $stmt = $connection->prepare(INS_TEMPERATURE);
    $stmt->bindParam(":value", $this->value, PDO::PARAM_STR);
    $stmt->bindParam(":units", $this->units, PDO::PARAM_STR);

    if (!$stmt->execute()) {
        echo "Temperature: " . $connection->error . "\n";
        return NULL;
    }

    return $connection->lastInsertID();
}

public function selectExistingID($connection) {
    $stmt = $connection->prepare(SEL_TEMPERATURE_IDTEMPERATURE);
    $stmt->bindParam(":value", $this->value, PDO::PARAM_STR);
    $stmt->bindParam(":units", $this->units, PDO::PARAM_STR);

    if (!$stmt->execute()) {
        echo "Temperature: " . $connection->error . "\n";
        return NULL;
    }

    return $stmt->fetchColumn();
}

/**************************************************************************************************/
/******************************************OUTPUT**************************************************/
/**************************************************************************************************/
    public function print($depth) {
        AstronomyData::printProperty("Value:", $this->value, $depth);
        AstronomyData::printProperty("Units:", $this->units, $depth);
    }
}

?>
