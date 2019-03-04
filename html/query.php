<?php
    include_once("AstronomyData/Common/DatabaseHelper.inc.php");
    include_once("AstronomyData/Common/SQLHelper.inc.php");

    define("XMLFILE", "out.xml");
    define("USERNAME", "AstrDataQuery");
    define("PASSWORD", "J84vBBHRGRhW8YNB");
    define("HOSTNAME", "localhost");
    define("DATABASE", "AstronomyData");

    $success = TRUE;

    //Connect to the database
    $connection = connectDatabase(HOSTNAME, USERNAME, PASSWORD, DATABASE);

    $sqlString = array();

    $sqlString[] = "SELECT * 
                    FROM UserEquipment 
                    LEFT JOIN Equipment
                    ON UserEquipment.idEquipment = Equipment.idEquipment
                    LEFT JOIN OpticalDevice
                    ON UserEquipment.idEquipment = OpticalDevice.idEquipment
                    LEFT JOIN Size
                    On OpticalDevice.aperture_idSize = Size.idSize
                    LEFT JOIN Note
                    ON UserEquipment.idNote = Note.idNote;";
                    


    foreach ($sqlString as $sql)
    {     
        $resultArray = $connection->query($sql);

        if (NULL == $resultArray)
        {
            $success = FALSE;
            echo "Could not query for: $sql <br/>". $connection->error;
            continue;
        }

        while ($row = $resultArray->fetch_assoc()) {
           echo "User: " . $row['idUser'] . "<br/>";
           echo "Equipment ID: " . $row['userIdentifiesAs'] . "<br/>";
           echo "Brand: " . $row['brand'] . "<br/>";
           echo "Type: " . $row['type'] . "<br/>";
           echo "Aperture: " . $row['value'] . " " . $row['units'] . "<br/>";
         
           echo "Note: " . $row['text'] . "<br/><br/>";
        }
    }

    return $success;   
?>
