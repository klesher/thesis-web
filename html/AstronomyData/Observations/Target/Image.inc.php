<?php

const INS_IMAGE = "INSERT INTO TargetImage
                    (idTarget, imageName, imageData, idNoteTargetImage)
                    VALUES
                    (:idTarget, :imageName, :imageData, :idNote)";

class Image {

    private $fileName = NULL;
    private $data = NULL;
    private $note = NULL;

/**************************************************************************************************/
/*****************************************CONSTRUCTORS*********************************************/
/**************************************************************************************************/
    public function __construct($simpleXML) {
        $this->fileName = (string)AstronomyData::simpleXMLToProperty($simpleXML->File);
        $this->note = AstronomyData::simpleXMLToObject($simpleXML->Note, "Note");
    }

/**************************************************************************************************/
/*****************************************FACILITATORS*********************************************/
/**************************************************************************************************/


/**************************************************************************************************/
/*****************************************DATABASE*************************************************/
/**************************************************************************************************/
public function toDatabase($connection, $idTarget) {
    $idNote = NULL;
    $imageDataTemp = "Junk";  //TODO: Replace with actual image data

    if (!empty($this->note) && !$this->note->isEmpty()) {
        $idNote = $this->note->toDatabase($connection);
    }

    $stmt = $connection->prepare(INS_IMAGE);

    $stmt->bindParam(":idTarget", $idTarget, PDO::PARAM_INT);
    $stmt->bindParam(":imageName", $this->fileName, PDO::PARAM_STR);
    $stmt->bindParam(":imageData", $imageDataTemp, PDO::PARAM_STR);
    $stmt->bindParam(":idNote", $idNote, PDO::PARAM_INT);

    if (!$stmt->execute()) {
        echo "TargetImage: " . $connection->error . "<br/>\n";
        return NULL;
    }

    return True;
}


/**************************************************************************************************/
/******************************************OUTPUT**************************************************/
/**************************************************************************************************/
    public function print($depth) {
        AstronomyData::printProperty("File:", $this->fileName, $depth);
        AstronomyData::printPropertyObject("Note:", $this->note, $depth);
        //AstronomyData::printProperty("Data:", $this->data, $depth);

    }

}

?>
