<?php
set_include_path('html/AstronomyData/Equipment/');
include_once(get_include_path() . "Telescope.inc.php");

set_include_path('html/AstronomyData/Observations/');
include_once(get_include_path() . "Observation.inc.php");

const INS_USEREQUIPMENT =  "INSERT INTO UserEquipment
                            (idUser, userIdentifiesAs, idEquipment, idNote)
                            VALUES(:idUser, :userIdentifiesAs, :idEquipment, :idNote)";

class AstronomyData {
    protected $equipmentList = array();
    protected $observationList = array();
    protected $idUser;

/**************************************************************************************************/
/*****************************************CONSTRUCTORS*********************************************/
/**************************************************************************************************/
    public function __construct($idUser, $simpleXML) {
        $this->idUser = $idUser;

        $this->equipmentList = self::simpleXMLToArray($simpleXML->Telescope, "Telescope", "ID");
        $this->observationList = self::simpleXMLToArray($simpleXML->Observation, "Observation", "Date");
    }

/**************************************************************************************************/
/*****************************************FACILITATORS*********************************************/
/**************************************************************************************************/
    public static function simpleXMLToProperty($simpleXML) {
        if($simpleXML)
            return $simpleXML;
        return NULL;
    }

    public static function simpleXMLToObject($simpleXML, $className){
        if ($simpleXML) {
            return new $className($simpleXML);
        }
        return NULL;
    }

    public static function simpleXMLToArray($simpleXML, $className, $uniqueID){
        $array = array();
        foreach ($simpleXML as $element) {
            if ($uniqueID && !$element->$uniqueID) {
                continue;
            }
            $array[] = new $className($element);
        }
        return $array;
    }

/**************************************************************************************************/
/*****************************************DATABASE*************************************************/
/**************************************************************************************************/
    public function toDatabase($connection) {
        $success = self::listToDatabase($connection, $this->equipmentList, "userEquipment");
        $success = self::listToDatabase($connection, $this->observationList, "observation");

        return $success;
    }

    function listToDatabase($connection, $list, $listType) {
        $success = True;
        $function = $listType . "ToDatabase";
        foreach ($list as $item) {
            //Ignore items without an ID
            if ($item->hasID()) {
                if (!self::$function($connection, $item)) {
                    $success = False;
                }
            } else {
                echo "WARN: Found $listType with no ID -- skipping.\n";
                $success = False;
            }
        }
        return $success;
    }

    //TODO: Create UserEquipment class.
    public function userEquipmentToDatabase($connection, $equipment) {
        $idEquipment = NULL;
        $idNote = NULL;

        //If the equipment is empty besides the ID, don't put it in the
        //database
        if (!$equipment->isEmpty()) {
            $idEquipment = $equipment->toDatabase($connection);
        }
        if ($equipment->hasNote()) {
            $idNote = $equipment->note->toDatabase($connection);
        }

        $stmt = $connection->prepare(INS_USEREQUIPMENT);
        $stmt->bindParam(":idUser", $this->idUser, PDO::PARAM_STR);
        $stmt->bindParam(":userIdentifiesAs", $equipment->id, PDO::PARAM_STR);
        $stmt->bindParam(":idEquipment", $idEquipment, PDO::PARAM_INT);
        $stmt->bindParam(":idNote", $idNote, PDO::PARAM_INT);

        if (!$stmt->execute()) {
            echo "User Equipment: $connection->error\n";
            return FALSE;
        }
        return TRUE;
    }

    //TODO: Move remove this when userEquipmentToDatabase goes to its own class.
    // This is just for consistency at the moment.
    public function observationToDatabase($connection, $observation) {
        return $observation->toDatabase($connection, $this->idUser);
    }

/**************************************************************************************************/
/******************************************OUTPUT**************************************************/
/**************************************************************************************************/
    public static function addTab($depth) {
        return str_repeat("    ", $depth);
    }
    public static function printHeader($name, $depth) {
        print self::addTab($depth) . "$name\n";
        print self::addTab($depth) . "==========\n";
    }

    public static function printFooter($depth) {
        print self::addTab($depth) . "----------\n\n";
    }

    public static function printProperty($name, $property, $depth) {
        if ($property) {
            print AstronomyData::addTab($depth) . "$name $property\n";
        }
    }

    public static function printPropertyObject($name, $property, $depth) {
        if ($property) {
            AstronomyData::printHeader("$name", $depth);
            $property->print($depth+1);
        }
    }


    public static function printList ($list, $header, $depth) {
        if ($list) {
            self::printHeader($header, $depth);
            foreach ($list as $value) {
                $value->print($depth+1);
                self::printFooter($depth+1);
            }
        }
    }

    public function print($depth) {
        self::printList($this->equipmentList, "Equipment", $depth);
        self::printList($this->observationList, "Observations", $depth);
    }
}
?>
