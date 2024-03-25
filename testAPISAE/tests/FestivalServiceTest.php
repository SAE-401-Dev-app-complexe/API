<?php
require '../API/FestivalService.php';
require 'classeUtilitaireTest.php';
use PHPUnit\Framework\TestCase;

class FestivalServiceTest extends TestCase
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

    public function testGetFestivalNoError()
    {
        //GIVEN mock objects for the PDO and PDOStatement classes, as for the data you want to return
        $mockPDO = $this->createMock(PDO::class);

        $mockStmt = $this->createMock(PDOStatement::class);
        $mockPDO->method('prepare')->willReturn($mockStmt);

        $mockFestival = [
            'id' => 1,
            'categorie' => 2,
            'titre' => 'titletest',
            'description' => 'logintest',
            'dateDebut' => '2024-05-01',
            'dateFin' => '2024-05-02'
        ];
        $mockStmt->method('fetchAll')->willReturn($mockFestival);



        // WHEN you call the getter
        $festival = FestivalService::getFestival($mockPDO, 'mockApiKey');

        // THEN you get the data you expect
        $this->assertEquals($mockFestival['id'], $festival['id']);
        $this->assertEquals($mockFestival['categorie'], $festival['categorie']);
        $this->assertEquals($mockFestival['titre'], $festival['titre']);
        $this->assertEquals($mockFestival['description'], $festival['description']);
        $this->assertEquals($mockFestival['dateDebut'], $festival['dateDebut']);
        $this->assertEquals($mockFestival['dateFin'], $festival['dateFin']);

    }
}
