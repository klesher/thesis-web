<?php

include_once("OpticalDevice.inc.php");

const INS_TELESCOPE = "INSERT INTO Telescope
                       (idEquipment, focalRatio)
                       VALUES (:idEquipment, :focalRatio)";

class Telescope extends OpticalDevice {
    private $focalRatio = NULL;

/**************************************************************************************************/
/*****************************************CONSTRUCTORS*********************************************/
/**************************************************************************************************/
    public function __construct($simpleXML) {
        parent::__construct($simpleXML);

        $this->focalRatio = (float)AstronomyData::simpleXMLToProperty($simpleXML->FocalRatio);
    }

    public function toDatabase($connection) {
        $idEquipment = parent::toDatabase($connection);
        $stmt = $connection->prepare(INS_TELESCOPE);
        $stmt->bindParam(":idEquipment", $idEquipment, PDO::PARAM_INT);
        $stmt->bindParam(":focalRatio", $this->focalRatio, PDO::PARAM_STR);

        if (!$stmt->execute()) {
            echo "Telescope: " . $connection->error . "\n";
            return NULL;
        }

        return $idEquipment;
    }

/**************************************************************************************************/
/*****************************************FACILITATORS*********************************************/
/**************************************************************************************************/
    public function isEmpty() {
        return parent::isEmpty() && empty($this->focalratio);
    }

/**************************************************************************************************/
/*****************************************DATABASE*************************************************/
/**************************************************************************************************/

/**************************************************************************************************/
/******************************************OUTPUT**************************************************/
/**************************************************************************************************/
    public function print($depth) {
        parent::print($depth);
        AstronomyData::printProperty("FocalRatio:", $this->focalRatio, $depth);
    }
}

?>
