<?php

class CelestialObject {

/**************************************************************************************************/
/*****************************************CONSTRUCTORS*********************************************/
/**************************************************************************************************/

/**************************************************************************************************/
/*****************************************FACILITATORS*********************************************/
/**************************************************************************************************/

/**************************************************************************************************/
/*****************************************DATABASE*************************************************/
/**************************************************************************************************/
     public static function determineID($connection, $properName, $designation, $coordinates) {
        $IDSByProperName  = NULL;
        $IDSByDesignation = NULL;
        $IDSByCoordinates = NULL;

        if (NULL == $connection)
            return NULL;

        /*************GATHER AS MUCH INFO ABOUT THE OBJECT AS POSSIBLE***************/
        if (NULL != $properName)
        {
            $designation = new Designation();
            $designation->setCatalog("Proper");
            $designation->setObject($properName);
            $IDSByProperName = $designation->determineID($connection);
        }
        if (NULL != $designation)
            $IDSByDesignation = $designation->determineID($connection);

//        if (NULL != $coordinates)
//            $IDSByCoordinates = $coordinates->determineID($connection);

        return self::processIDS($IDSByProperName, $IDSByDesignation, $IDSByCoordinates);
     }

    private static function processIDS($IDSByProperName, $IDSByDesignation, $IDSByCoordinates)
    {
        //For now, just go based off of the proper name or designation ID.
        //Eventually this will be an array of IDs
        if ($IDSByProperName > 0)
            return $IDSByProperName;

        if ($IDSByDesignation > 0)
            return $IDSByDesignation;

        return NULL;
    }

/**************************************************************************************************/
/******************************************OUTPUT**************************************************/
/**************************************************************************************************/

}

?>
