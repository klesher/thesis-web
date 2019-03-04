<?php

set_include_path('html/AstronomyData/Common/');
include_once(get_include_path() . "Note.inc.php");

set_include_path('html/AstronomyData/Observations/');
include_once(get_include_path() . "Wind.inc.php");
include_once(get_include_path() . "Temperature.inc.php");
include_once(get_include_path() . "TimeRange.inc.php");
include_once(get_include_path() . "Date.inc.php");
include_once(get_include_path() . "Location.inc.php");
include_once(get_include_path() . "Coordinates.inc.php");

include_once(get_include_path() . "Target/Target.inc.php");


const INS_OBSERVATION = "INSERT INTO Observation
                         (idUser, date, time, idLocation, weather, seeing, transparency,
                          humidity, idWind, idTemperature, idNoteObservation)
                         VALUES
                         (:idUser, :date, :time, :idLocation, :weather, :seeing, :transparency,
                          :humidity, :idWind, :idTemperature, :idNote)";

class Observation{
    protected $idUser = NULL;
    private $date = NULL;
    private $timeRange = NULL;
    private $time = NULL;
    private $weather = NULL;
    private $seeing = NULL;
    private $transparency = NULL;
    private $humidity = NULL;
    private $location = NULL;
    private $wind = NULL;
    private $temperature = NULL;
    private $note = NULL;

    private $targetList = array();


/**************************************************************************************************/
/*****************************************CONSTRUCTORS*********************************************/
/**************************************************************************************************/
    public function __construct($simpleXML) {
        if (!$simpleXML) {
            return;
        }

        $this->date = AstronomyData::simpleXMLToObject($simpleXML->Date, "Date");
        $this->timeRange = AstronomyData::simpleXMLToObject($simpleXML->TimeRange, "TimeRange");

        if (empty($this->timeRange)) {
            $this->time = AstronomyData::simpleXMLToObject($simpleXML->Time, "Time");
        }

        $this->location = AstronomyData::simpleXMLToObject($simpleXML, "Location");

        $this->seeing = (int)AstronomyData::simpleXMLToProperty($simpleXML->Seeing);
        $this->transparency = (int)AstronomyData::simpleXMLToProperty($simpleXML->Transparency);
        $this->weather = (string)AstronomyData::simpleXMLToProperty($simpleXML->Weather);
        $this->humidity = (float)AstronomyData::simpleXMLToProperty($simpleXML->Humidity);
        $this->temperature = AstronomyData::simpleXMLToObject($simpleXML->Temperature, "Temperature");
        $this->wind = AstronomyData::simpleXMLToObject($simpleXML->Wind, "Wind");
        $this->note = AstronomyData::simpleXMLToObject($simpleXML->Note, "Note");

        $this->targetList = AstronomyData::simpleXMLToArray($simpleXML->Target, "Target", "ID");
    }

/**************************************************************************************************/
/*****************************************FACILITATORS*********************************************/
/**************************************************************************************************/
    public function hasID(){
        //DATE is explicitly required.  If it's missing, everything should be
        //considered missing.
        return !empty($this->date) && !$this->date->isEmpty();
    }

/**************************************************************************************************/
/*****************************************DATABASE*************************************************/
/**************************************************************************************************/
//TODO: Revamp this to use insertNew/SelectExistingID model
public function toDatabase($connection, $idUser) {
    $temp = NULL;
    $time = NULL;
    $idWind = NULL;
    $idTemperature = NULL;
    $idNote = NULL;

    if (empty($this->date) || $this->date->isEmpty()) {
        echo "ERROR - Observation Date does not exist.";
        return FALSE;
    }

    $date = $this->date->toString();

    if (!empty($this->timeRange) && !$this->timeRange->isEmpty()) {
        $time = $this->timeRange->toString();
    } else if (!empty($this->time)){
        $time = $this->time->toString();
    }

    if (!empty($this->wind) && !$this->wind->isEmpty()) {
        $idWind = $this->wind->toDatabase($connection);
    }

    if (!empty($this->temperature) && !$this->temperature->isEmpty()) {
        $idTemperature = $this->temperature->toDatabase($connection);
    }

    if (!empty($this->note) && !$this->note->isEmpty()) {
        $idNote = $this->note->toDatabase($connection);
    }

    $stmt = $connection->prepare(INS_OBSERVATION);

    $stmt->bindParam(":idUser", $idUser, PDO::PARAM_STR);
    $stmt->bindParam(":date", $date, PDO::PARAM_STR);
    $stmt->bindParam(":time", $time, PDO::PARAM_STR);
    $stmt->bindParam(":idLocation", $temp, PDO::PARAM_INT);
    $stmt->bindParam(":weather", $this->weather, PDO::PARAM_STR);
    $stmt->bindParam(":seeing", $this->seeing, PDO::PARAM_INT);
    $stmt->bindParam(":transparency", $this->transparency, PDO::PARAM_INT);
    $stmt->bindParam(":humidity", $this->humidity, PDO::PARAM_STR);
    $stmt->bindParam(":idWind", $idWind, PDO::PARAM_INT);
    $stmt->bindParam(":idTemperature", $idTemperature, PDO::PARAM_INT);
    $stmt->bindParam(":idNote", $idNote, PDO::PARAM_INT);

    if (!$stmt->execute()) {
        echo "Observation: " . $connection->error . "<br/>\n";
        return NULL;
    }

    $idObservation = $connection->lastInsertID();

    if (!$this->targetListToDatabase($connection, $idObservation)) {
        return False;
    }
    return True;
}

public function targetListToDatabase($connection, $idObservation) {
    $success = True;
    foreach ($this->targetList as $target) {
        if (!$target->toDatabase($connection, $idObservation)) {
            $success = False;
        }
    }

    return $success;
}

/**************************************************************************************************/
/******************************************OUTPUT**************************************************/
/**************************************************************************************************/
    public function print($depth) {
        AstronomyData::printPropertyObject("Date", $this->date, $depth);

        if ($this->timeRange) {
            AstronomyData::printPropertyObject("TimeRange", $this->timeRange, $depth);
        } else if ($this->time) {
            AstronomyData::printPropertyObject("Time", $this->time, $depth);
        }
        if ($this->location  && !$this->location->isEmpty()) {
            AstronomyData::printPropertyObject("Location:", $this->location, $depth);
        }

        AstronomyData::printProperty("Seeing:", $this->seeing, $depth);
        AstronomyData::printProperty("Transparency:", $this->transparency, $depth);
        AstronomyData::printProperty("Weather:", $this->weather, $depth);
        AstronomyData::printProperty("Humidity:", $this->humidity, $depth);
        AstronomyData::printPropertyObject("Temperature", $this->temperature, $depth);
        AstronomyData::printPropertyObject("Wind", $this->wind, $depth);
        AstronomyData::printPropertyObject("Note", $this->note, $depth);

        AstronomyData::printList($this->targetList, "Targets", $depth);

    }
}

?>
