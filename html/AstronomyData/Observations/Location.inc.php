<?php

class Location {
    private $locationString = NULL;
    private $latitude = NULL;
    private $longitude = NULL;

/**************************************************************************************************/
/*****************************************CONSTRUCTORS*********************************************/
/**************************************************************************************************/
    //TODO: Convert Latitude and Longitude to common measure.
    public function __construct($simpleXML) {
        if ($simpleXML) {
            $this->locationString = AstronomyData::simpleXMLToProperty($simpleXML->Location);

            //Only set Latitude or Longitude if both exist;
            if ($simpleXML->Latitude && $simpleXML->Longitude) {
                $this->latitude = AstronomyData::simpleXMLToObject($simpleXML->Latitude, "GeoCoordinate");
                $this->longitude = AstronomyData::simpleXMLToObject($simpleXML->Longitude, "GeoCoordinate");
            }
        }
    }

/**************************************************************************************************/
/*****************************************FACILITATORS*********************************************/
/**************************************************************************************************/
    public function isEmpty() {
        return empty($this->latitude) && empty($this->longitude) && empty($this->locationString);
    }

/**************************************************************************************************/
/*****************************************DATABASE*************************************************/
/**************************************************************************************************/
/*
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
*/
/**************************************************************************************************/
/******************************************OUTPUT**************************************************/
/**************************************************************************************************/
    public function print($depth) {
        AstronomyData::printProperty("String:", $this->locationString, $depth);
        AstronomyData::printPropertyObject("Latitude", $this->latitude, $depth);
        AstronomyData::printPropertyObject("Longitude", $this->longitude, $depth);
    }
}

?>
