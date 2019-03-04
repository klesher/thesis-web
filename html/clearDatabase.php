<?php

include_once("PDOHelper.inc.php");

# Drop table permissions required for TRUNCATE
const USERNAME = "AstrDataEmpty";
const PASSWORD = "Tn2yFPcAamAzwFB3";
const HOSTNAME = "localhost";
const DATABASE = "AstronomyData";
const CHARSET = "utf8mb4";

//Connect to the database
$connection = connectDatabase(HOSTNAME, USERNAME, PASSWORD, DATABASE, CHARSET);

$sqlString = array();

$sqlString[] = "SET FOREIGN_KEY_CHECKS = 0";
$sqlString[] = "TRUNCATE TABLE Binoculars";
$sqlString[] = "TRUNCATE TABLE Equipment";
$sqlString[] = "TRUNCATE TABLE Eyepiece";
$sqlString[] = "TRUNCATE TABLE Mount";
$sqlString[] = "TRUNCATE TABLE Note";
$sqlString[] = "TRUNCATE TABLE Observation";
$sqlString[] = "TRUNCATE TABLE OpticalDevice";
$sqlString[] = "TRUNCATE TABLE Size";
$sqlString[] = "TRUNCATE TABLE Target";
$sqlString[] = "TRUNCATE TABLE TargetImage";
$sqlString[] = "TRUNCATE TABLE Telescope";
$sqlString[] = "TRUNCATE TABLE Temperature";
$sqlString[] = "TRUNCATE TABLE User";
$sqlString[] = "TRUNCATE TABLE UserEquipment";
$sqlString[] = "TRUNCATE TABLE UserFollows";
$sqlString[] = "TRUNCATE TABLE Wind";
$sqlString[] = "SET FOREIGN_KEY_CHECKS = 1";

    foreach ($sqlString as $sql) {
         $stmt = $connection->prepare($sql);
        if (!$stmt->execute()) {
            echo "Query: $sql:<br/> " . $connection->error . "<br/>\n";
            continue;
        }
    }

    echo "Truncation completed!\n";
?>
