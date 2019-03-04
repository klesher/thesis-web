<?php

class Time {
    private $hour = NULL;
    private $minute = NULL;
    private $second = NULL;

/**************************************************************************************************/
/*****************************************CONSTRUCTORS*********************************************/
/**************************************************************************************************/
    public function __construct($simpleXML) {
        $this->hour = AstronomyData::simpleXMLToProperty($simpleXML->Hour);
        $this->minute = AstronomyData::simpleXMLToProperty($simpleXML->Minute);
        $this->second = "00";
        //$this->second = AstronomyData::simpleXMLToProperty($simpleXML->Second);
    }

/**************************************************************************************************/
/*****************************************FACILITATORS*********************************************/
/**************************************************************************************************/
    public function isEmpty() {
        return empty($this->hour) || empty($this->minute) || empty($this->second);
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

        return $this->hour . ":" . $this->minute . ":" . $this->second;
    }

    public function print($depth) {
        AstronomyData::printProperty("Hour:", $this->hour, $depth);
        AstronomyData::printProperty("Minute:", $this->minute, $depth);
        AstronomyData::printProperty("Second:", $this->second, $depth);

    }
}

?>
