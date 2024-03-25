<?php

function getPDO(): PDO
{
    $host = 'localhost';
    $dbName = 'festiplanbfgi_sae';
    $dbCharset = 'utf8mb4';
    $dbPort = '3306';
    $user = 'root';
    $pass = 'root';
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];

    $dsn = "mysql:host=$host;dbname=$dbName;charset=$dbCharset;port=$dbPort";

    try {
        return new PDO($dsn, $user, $pass, $options);
    } catch (PDOException $e) {
        throw new PDOException('Database connection failed');
    }
}
