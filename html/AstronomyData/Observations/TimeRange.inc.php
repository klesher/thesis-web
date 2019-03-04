<?php

include_once("html/AstronomyData/Observations/Time.inc.php");

class TimeRange {
    private $startTime = NULL;
    private $endTime = NULL;

/**************************************************************************************************/
/*****************************************CONSTRUCTORS*********************************************/
/**************************************************************************************************/
    public function __construct($simpleXML) {
        $this->startTime = AstronomyData::simpleXMLToObject($simpleXML->Time[0], "Time");
        $this->endTime = AstronomyData::simpleXMLToObject($simpleXML->Time[1], "Time");
    }

/**************************************************************************************************/
/*****************************************FACILITATORS*********************************************/
/**************************************************************************************************/
    public function getStartTime() {
        return $this->startTime;
    }

    public function getEndTime() {
        return $this->endTime;
    }

    public function isEmpty() {
        //return empty($this->startTime) || empty($this->endTime) ||
        //    $this->startTime->isEmpty() || $this->endTime->isEmpty();
        return empty($this->startTime) || $this->startTime->isEmpty();
    }

/**************************************************************************************************/
/*****************************************DATABASE*************************************************/
/**************************************************************************************************/

/**************************************************************************************************/
/******************************************OUTPUT**************************************************/
/**************************************************************************************************/
    public function toString() {
        if (self::isEmpty()) {
            return NULL;
        }

        //TODO: Fix TimeRange for Observation time in database.
        //return $this->startTime->toString() . " to " . $this->endTime->toString();
        return $this->startTime->toString();
    }
    public function print($depth) {
        AstronomyData::printPropertyObject("Time Start", $this->startTime, $depth);
        AstronomyData::printPropertyObject("Time End", $this->endTime, $depth);
    }
}

?>
