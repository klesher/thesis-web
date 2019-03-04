<?php

const SEL_IDCELESTOBJ = "SELECT idCelestialObject
                         FROM CelestialObjectCatalog
                         WHERE nameCatalog = :nameCatalog
                         AND idCatalog = :idCatalog
                         LIMIT 1";

//TODO: Rename this class or MySQL table name to match up better.
class Designation {
    private $catalogName;
    private $catalogID;

/**************************************************************************************************/
/*****************************************CONSTRUCTORS*********************************************/
/**************************************************************************************************/
    public function __construct($simpleXML) {
        $this->catalogName = (string)AstronomyData::simpleXMLToProperty($simpleXML->Catalog);
        $this->catalogID = (string)AstronomyData::simpleXMLToProperty($simpleXML->Object);
    }

/**************************************************************************************************/
/*****************************************FACILITATORS*********************************************/
/**************************************************************************************************/
public function isEmpty() {
    return empty($this->catalogName) || empty($this->catalogID);
}
/**************************************************************************************************/
/*****************************************DATABASE*************************************************/
/**************************************************************************************************/
public function toDatabase($connection) {
    $id = $this->selectExistingID($connection);

    if (!$id)
        return NULL;
       //return $this->insertNew($connection);
    return $id;
}
/*
//Don't allow new insertions for the time being (or ever?)
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
*/

public function selectExistingID($connection) {
    $stmt = $connection->prepare(SEL_IDCELESTOBJ);
    $stmt->bindParam(":nameCatalog", $this->catalogName, PDO::PARAM_STR);
    $stmt->bindParam(":idCatalog", $this->catalogID, PDO::PARAM_STR);

    if (!$stmt->execute()) {
        echo "Designation: $connection->error\n";
        return NULL;
    }

    return $stmt->fetchColumn();
}


/**************************************************************************************************/
/******************************************OUTPUT**************************************************/
/**************************************************************************************************/
    public function print($depth) {
        AstronomyData::printProperty("Catalog:", $this->catalogName, $depth);
        AstronomyData::printProperty("Object:", $this->catalogID, $depth);
    }
}

?>
