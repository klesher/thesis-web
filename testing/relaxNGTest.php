<?php

const DEBUG = False;
const FILE_XML = "testing/relaxNGTest.xml";
const FILE_RELAXNG = "html/grammar.rng";

const DB_USERNAME = "AstronomyDataAdd";
const DB_PASSWORD = "H4fHKjxTFA4XB6zq";
const DB_HOSTNAME = "localhost";
const DB_DATABASE = "AstronomyData";
const DB_CHARSET = "utf8mb4";

const USERNAME = "kevin.lesher";
const PASSWORD = "plaintext=gross";
const FIRSTNAME = "Kevin";
const LASTNAME = "Lesher";

include_once("html/AstronomyData/AstronomyData.inc.php");
include_once("html/AstronomyData/User/User.inc.php");

include_once("html/PDOHelper.inc.php");

$dom = new DOMDocument;
$dom->preserveWhiteSpace = False;
$dom->load(FILE_XML);

if (!$dom->relaxNGValidate(FILE_RELAXNG)) {
	echo "Bad XML\n";
    return 1;
}
// Load xml data.
$xml = file_get_contents(FILE_XML);
// Strip whitespace between xml tags
$xml = preg_replace('~\s*(<([^-->]*)>[^<]*<!--\2-->|<[^>]*>)\s*~','$1',$xml);
// Convert CDATA into xml nodes.
$simpleXML = simplexml_load_string($xml,'SimpleXMLElement', LIBXML_NOCDATA);

if (!$simpleXML) {
    echo "No AstronomyData.  Exiting\n";
    return 1;
}

$user = new User(USERNAME, PASSWORD, FIRSTNAME, LASTNAME);
$astronomyData = new AstronomyData(USERNAME, $simpleXML);

if (DEBUG) {
    $astronomyData->print(0);
}

//Connect to the database
$connection = connectDatabase(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_CHARSET);

if ($user->toDatabase($connection) && $astronomyData->toDatabase($connection)) {
    echo "Successfully inserted AstronomyData in file [" . FILE_XML . "] to the database!\n";
}
else {
    echo "Error: COULD NOT INSERT AstronomyData in file [" . FILE_XML . "] to the database!\n";
}


return 0;
?>
