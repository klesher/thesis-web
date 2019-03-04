<?php

function connectDatabase($hostName, $userName, $password, $database, $charset) {
    $dsn = "mysql:host=$hostName;dbname=$database;charset=$charset";

    $options = [ PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                 PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                 PDO::ATTR_EMULATE_PREPARES   => false,];

    try {
        return new PDO($dsn, $userName, $password, $options);
    } catch (PDOException $e) {
        throw new PDOException($e->getMessage(), (int) $e->getCode());
    }
}
 ?>
