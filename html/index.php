<?php

set_include_path('AstronomyData/');
include_once(get_include_path() . "AstronomyData.inc.php");


include_once("DatabaseHelper.inc.php");

define("XMLFILE", "testData.ref.xml");
define("USERNAME", "AstronomyDataAdd");
define("PASSWORD", "H4fHKjxTFA4XB6zq");
define("HOSTNAME", "localhost");
define("DATABASE", "AstronomyData");

$astronomyData = new AstronomyData("kev92486");

//Walk and read the XML tree from the file.
$astronomyData->fromXML(XMLFILE);

//Connect to the database
$connection = connectDatabase(HOSTNAME, USERNAME, PASSWORD, DATABASE);

if ($astronomyData->toDatabase($connection))
    echo "Successfully inserted AstronomyData in file [" . XMLFILE . "] to the database!<br/><br/>";
else
    echo "<b>Error: COULD NOT INSERT AstronomyData in file [" . XMLFILE . "] to the database!</b><br/><br/>";

$astronomyData->printNodes();
?>
