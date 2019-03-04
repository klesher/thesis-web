<?php

include_once("html/AstronomyData/Observations/Target/Designation.inc.php");
include_once("html/AstronomyData/Observations/Target/Image.inc.php");

include_once("html/AstronomyData/CelestialObjects/CelestialObject.inc.php");

include_once("html/AstronomyData/Common/Note.inc.php");


const INS_TARGET = "INSERT INTO Target
                    (idObservation, timeStart, timeEnd, idNoteTarget, idCelestialObject, equipmentUsed)
                    VALUES
                    (:idObservation, :timeStart, :timeEnd, :idNote, :idCelestialObject, :equipmentID)";

class Target {
    private $id = NULL;
    private $timeRange = NULL;
    private $time = NULL;
    private $equipmentID = NULL;
    private $type = NULL;
    private $altitude = NULL;
    private $azimuth = NULL;
    private $rightAscension = NULL;
    private $declination = NULL;
    private $constellation = NULL;
    private $filter = NULL;
    private $designation = NULL;
    private $magnitude = NULL;
    private $note = NULL;

    private $directory = NULL;
    private $directoryNote = NULL;

    private $imageList = array();

/**************************************************************************************************/
/*****************************************CONSTRUCTORS*********************************************/
/**************************************************************************************************/
    public function __construct($simpleXML) {
        $this->id = AstronomyData::simpleXMLToProperty($simpleXML->ID);

        $this->timeRange = AstronomyData::simpleXMLToObject($simpleXML->TimeRange, "TimeRange");
        if (empty($this->timeRange)) {
            $this->time = AstronomyData::simpleXMLToObject($simpleXML->Time, "Time");
        }

        $this->equipmentID = (string)AstronomyData::simpleXMLToProperty($simpleXML->TelescopeID);
        $this->type = (string)AstronomyData::simpleXMLToProperty($simpleXML->Type);
        $this->constellation = (string)AstronomyData::simpleXMLToProperty($simpleXML->Constellation);
        $this->filter = (string)AstronomyData::simpleXMLToProperty($simpleXML->Filter);

        if ($simpleXML->Magnitude) {
            $this->magnitude = (float)AstronomyData::simpleXMLToProperty($simpleXML->Magnitude);

            if ($simpleXML->Magnitude->attributes()->{"Sign"} == "-") {
                $this->magnitude = (float)$this->magnitude * -1.0;
            }
        }

        $this->altitude = AstronomyData::simpleXMLToObject($simpleXML->Altitude->Angle, "Angle");
        $this->azimuth = AstronomyData::simpleXMLToObject($simpleXML->Azimuth->Angle, "Angle");
        $this->rightAscension = AstronomyData::simpleXMLToObject($simpleXML->RightAscension->Angle, "Angle");
        $this->declination = AstronomyData::simpleXMLToObject($simpleXML->Declination->Angle, "Angle");
        $this->designation = AstronomyData::simpleXMLToObject($simpleXML->Designation, "Designation");
        $this->note = AstronomyData::simpleXMLToObject($simpleXML->Note, "Note");


        if ($simpleXML->Directory) {
            $this->directory = (string)AstronomyData::simpleXMLToProperty($simpleXML->Directory->Path);
            $this->directoryNote = AstronomyData::simpleXMLToObject($simpleXML->Directory->Note, "Note");
            $this->imageList = AstronomyData::simpleXMLToArray($simpleXML->Directory->Image, "Image", "File");
        }
    }

/**************************************************************************************************/
/*****************************************FACILITATORS*********************************************/
/**************************************************************************************************/


/**************************************************************************************************/
/*****************************************DATABASE*************************************************/
/**************************************************************************************************/
//TODO: Revamp this to use insertNew/SelectExistingID model
public function toDatabase($connection, $idObservation) {
    $timeStart = NULL;
    $timeEnd = NULL;
    $idNote = NULL;
    $idCelestialObject = NULL;

    if (!empty($this->note) && !$this->note->isEmpty()) {
        $idNote = $this->note->toDatabase($connection);
    }

    if (!empty($this->timeRange) && !$this->timeRange->isEmpty()) {
        $timeStart = $this->timeRange->getStartTime()->toString();
        $timeEnd = $this->timeRange->getEndTime()->toString();
    } else if (!empty($this->time)){
        $timeStart = $this->time->toString();
    }

    if (!empty($this->designation) && !$this->designation->isEmpty()){
        $idCelestialObject = $this->designation->toDatabase($connection);
    }

    $stmt = $connection->prepare(INS_TARGET);

    $stmt->bindParam(":idObservation", $idObservation, PDO::PARAM_STR);
    $stmt->bindParam(":timeStart", $timeStart, PDO::PARAM_STR);
    $stmt->bindParam(":timeEnd", $timeEnd, PDO::PARAM_STR);
    $stmt->bindParam(":idCelestialObject", $idCelestialObject, PDO::PARAM_INT);
    $stmt->bindParam(":equipmentID", $this->equipmentID, PDO::PARAM_INT);
    $stmt->bindParam(":idNote", $idNote, PDO::PARAM_INT);

    if (!$stmt->execute()) {
        echo "Target: " . $connection->error . "\n";
        return NULL;
    }


    $idTarget= $connection->lastInsertID();

    if (!$this->imageListToDatabase($connection, $idTarget)) {
        return False;
    }

    return True;
}

public function imageListToDatabase($connection, $idTarget) {
    $success = True;
    foreach ($this->imageList as $image) {
        if (!$image->toDatabase($connection, $idTarget)) {
            $success = False;
        }
    }

    return $success;
}


/**************************************************************************************************/
/******************************************OUTPUT**************************************************/
/**************************************************************************************************/
    public function print($depth) {
        AstronomyData::printProperty("ID:", $this->id, $depth);

        if ($this->timeRange) {
            AstronomyData::printPropertyObject("TimeRange", $this->timeRange, $depth);
        } else if ($this->time) {
            AstronomyData::printPropertyObject("Time", $this->time, $depth);
        }

        AstronomyData::printProperty("EquipmentID:", $this->equipmentID, $depth);
        AstronomyData::printProperty("Type:", $this->type, $depth);
        AstronomyData::printProperty("Constellation:", $this->constellation, $depth);
        AstronomyData::printProperty("Filter:", $this->filter, $depth);
        AstronomyData::printProperty("Magnitude:", $this->magnitude, $depth);

        AstronomyData::printPropertyObject("Altitude:", $this->altitude, $depth);
        AstronomyData::printPropertyObject("Azimuth:", $this->azimuth, $depth);
        AstronomyData::printPropertyObject("Designation:", $this->designation, $depth);
        AstronomyData::printPropertyObject("RightAscension:", $this->rightAscension, $depth);
        AstronomyData::printPropertyObject("Declination:", $this->declination, $depth);
        AstronomyData::printPropertyObject("Note:", $this->note, $depth);

        AstronomyData::printProperty("Directory", $this->directory, $depth);
        AstronomyData::printPropertyObject("DirectoryNote", $this->directoryNote, $depth);

        AstronomyData::printList($this->imageList, "Images", $depth);
    }
}

?>
