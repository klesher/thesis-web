<?php

class Angle {
    private $type = NULL;
    private $degrees = NULL;
    private $hours = NULL;
    private $minutes = NULL;
    private $seconds = NULL;

/**************************************************************************************************/
/*****************************************CONSTRUCTORS*********************************************/
/**************************************************************************************************/
    public function __construct($simpleXML) {
        if (!$simpleXML) {
            return;
        }
        $this->type = AstronomyData::simpleXMLToProperty($simpleXML->attributes()->{"Type"});

        switch($this->type) {
            case "sexagesimal":
                $this->degrees = (int)AstronomyData::simpleXMLToProperty($simpleXML->Degrees);
                $this->minutes = (int)AstronomyData::simpleXMLToProperty($simpleXML->Minutes);
                $this->seconds = (float)AstronomyData::simpleXMLToProperty($simpleXML->Seconds);

                if ($simpleXML->attributes()->{"Sign"} == "-" && $degrees) {
                    $this->degrees = (float)$this->degrees * -1.0;
                }
                break;
            case "decimal":
                $this->degrees = AstronomyData::simpleXMLToProperty($simpleXML);

                if ($simpleXML->attributes()->{"Sign"} == "-" && $degrees) {
                    $this->degrees = (float)$this->degrees * -1.0;
                }
                break;
            case "hours":
                $this->hours = (int)AstronomyData::simpleXMLToProperty($simpleXML->Hours);
                $this->minutes = (int)AstronomyData::simpleXMLToProperty($simpleXML->Minutes);
                $this->seconds = (float)AstronomyData::simpleXMLToProperty($simpleXML->Seconds);
                break;
        }
    }


/**************************************************************************************************/
/*****************************************FACILITATORS*********************************************/
/**************************************************************************************************/

/**************************************************************************************************/
/*****************************************DATABASE*************************************************/
/**************************************************************************************************/

/**************************************************************************************************/
/******************************************OUTPUT**************************************************/
/**************************************************************************************************/
    public function print($depth) {
        AstronomyData::printHeader("Angle", $depth);

        switch($this->type) {
            case "sexagesimal":
                AstronomyData::printProperty("Degrees:", $this->degrees, $depth+1);
                AstronomyData::printProperty("Minutes:", $this->minutes, $depth+1);
                AstronomyData::printProperty("Seconds:", $this->seconds, $depth+1);
                break;
            case "decimal":
                AstronomyData::printProperty("Degrees:", $this->degrees, $depth+1);
                break;
            case "hours":
                AstronomyData::printProperty("Hours:", $this->hours, $depth+1);
                AstronomyData::printProperty("Minutes:", $this->minutes, $depth+1);
                AstronomyData::printProperty("Seconds:", $this->seconds, $depth+1);
                break;
        }
    }
}

?>
