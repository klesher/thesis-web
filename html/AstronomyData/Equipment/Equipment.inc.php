<?php

include_once("html/AstronomyData/Common/Note.inc.php");

const INS_EQUIPMENT = "INSERT INTO Equipment (brand, type, model)
                       VALUES (:brand, :type, :model)";

class Equipment extends AstronomyData {
    protected $id = NULL;
    protected $brand = NULL;
    protected $type = NULL;
    protected $model = NULL;
    protected $note = NULL;

/**************************************************************************************************/
/*****************************************CONSTRUCTORS*********************************************/
/**************************************************************************************************/
    public function __construct($simpleXML) {
        $this->id = (string)AstronomyData::simpleXMLToProperty($simpleXML->ID);
        $this->brand = (string)AstronomyData::simpleXMLToProperty($simpleXML->Brand);
        $this->type = (string)AstronomyData::simpleXMLToProperty($simpleXML->Type);
        $this->model = (string)AstronomyData::simpleXMLToProperty($simpleXML->Model);
        
        $this->note = AstronomyData::simpleXMLToObject($simpleXML->Note, "Note");
    }

/**************************************************************************************************/
/*****************************************FACILITATORS*********************************************/
/**************************************************************************************************/
    public function hasID() {
        return !empty($this->id);
    }

    public function hasNote() {
        return !empty($this->note) && !$this->note->isEmpty();
    }

    public function isEmpty() {
        return empty($this->brand) && empty($this->type) && empty($this->model) &&
        (empty($this->note) || $this->note->isEmpty());
    }

/**************************************************************************************************/
/*****************************************DATABASE*************************************************/
/**************************************************************************************************/
    public function toDatabase($connection) {
        $stmt = $connection->prepare(INS_EQUIPMENT);
        $stmt->bindParam(":brand", $this->brand, PDO::PARAM_STR);
        $stmt->bindParam(":type", $this->type, PDO::PARAM_STR);
        $stmt->bindParam(":model", $this->model, PDO::PARAM_STR);

        if (!$stmt->execute()) {
            echo "Equipment: " . $connection->error . "<br/>\n";
            return NULL;
        }

        return $connection->lastInsertID();
    }

/**************************************************************************************************/
/******************************************OUTPUT**************************************************/
/**************************************************************************************************/
    public function print($depth) {
        AstronomyData::printProperty("ID:", $this->id, $depth);
        AstronomyData::printProperty("Brand:", $this->brand, $depth);
        AstronomyData::printProperty("Type:", $this->type, $depth);
        AstronomyData::printProperty("Model:", $this->model, $depth);
        AstronomyData::printPropertyObject("Note:", $this->note, $depth);
    }
}

?>
