<?php

include_once("Equipment.inc.php");
include_once("Size.inc.php");

const INS_OPTICALDEVICE = "INSERT INTO OpticalDevice
                           (idEquipment, aperture_idSize, focalLength_idSize)
                           VALUES (:idEquipment, :idAperture, :idFocalLength)";

class OpticalDevice extends Equipment {
    protected $aperture = NULL;
    protected $focalLength = NULL;
    protected $mount = NULL;

/**************************************************************************************************/
/*****************************************CONSTRUCTORS*********************************************/
/**************************************************************************************************/
    public function __construct($simpleXML) {
        parent::__construct($simpleXML);

        $this->aperture = AstronomyData::simpleXMLToObject($simpleXML->Aperture, "Size");
        $this->focalLength = AstronomyData::simpleXMLToObject($simpleXML->FocalLength, "Size");

        $this->mount = (string)AstronomyData::simpleXMLToProperty($simpleXML->Mount);
    }

/**************************************************************************************************/
/*****************************************FACILITATORS*********************************************/
/**************************************************************************************************/
    public function isEmpty() {
        return parent::isEmpty() && empty($this->aperture) && empty($this->focalLength) &&
        empty($this->mount);
    }

/**************************************************************************************************/
/*****************************************DATABASE*************************************************/
/**************************************************************************************************/
    public function toDatabase($connection) {
        $idEquipment = parent::toDatabase($connection);
        $idAperture = NULL;
        $idFocalLength = NULL;

        if (!empty($this->aperture) && !$this->aperture->isEmpty()) {
            $idAperture = $this->aperture->toDatabase($connection);
        }
        if (!empty($this->focalLength) && !$this->focalLength->isEmpty()) {
            $idFocalLength = $this->focalLength->toDatabase($connection);
        }

        $stmt = $connection->prepare(INS_OPTICALDEVICE);
        $stmt->bindParam(":idEquipment", $idEquipment, PDO::PARAM_INT);
        $stmt->bindParam(":idAperture", $idAperture, PDO::PARAM_INT);
        $stmt->bindParam(":idFocalLength", $idFocalLength, PDO::PARAM_INT);


        if (!$stmt->execute()) {
            echo "OpticalDevice: " . $connection->error . "\n";
            return NULL;
        }

        return $idEquipment;
    }

    /**************************************************************************************************/
    /******************************************OUTPUT**************************************************/
    /**************************************************************************************************/
    public function print($depth) {
        parent::print($depth);

        AstronomyData::printPropertyObject("Aperture:", $this->aperture, $depth);
        AstronomyData::printPropertyObject("FocalLength:", $this->focalLength, $depth);
        AstronomyData::printProperty("Mount:", $this->mount, $depth);
    }

}

?>
