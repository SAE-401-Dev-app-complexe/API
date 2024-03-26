<?php


use PHPUnit\Framework\TestCase;

class FavorisServiceTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();
        // GIVEN a pdo for tests
        $host = 'localhost';
        $dbName = 'festiplanbfgi_sae';
        $dbCharset = 'utf8mb4';
        $dbPort = '3306';
        $user = 'root';
        $pass = 'root';
        $datasource = "mysql:host=$host;dbname=$dbName;charset=$dbCharset;port=$dbPort";
        $this->pdo = new PDO($datasource, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    }

    piblic function beforeEach(): void
    {

    }
}
