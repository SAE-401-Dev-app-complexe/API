<?php
require '../API/FestivalService.php';
require 'classeUtilitaireTest.php';
require '../API/database.php';
use PHPUnit\Framework\TestCase;

class FestivalServiceTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();
        // GIVEN a pdo for tests
        $this->pdo = getPDO();
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

    public function testGetDetailsFestivalNoError()
    {
        //GIVEN mock objects for the PDO and PDOStatement classes, as for the data you want to return
        $mockPDO = $this->createMock(PDO::class);

        $mockStmt = $this->createMock(PDOStatement::class);
        $mockPDO->method('prepare')->willReturn($mockStmt);

        $mockSpectacleFestival = [
            'idSpectacle' => 1,
            'idFestival' => 1
        ];
        $mockStmt->method('fetchAll')->willReturn($mockSpectacleFestival);

        // WHEN you call the getter
        $festival = FestivalService::getDetailsFestival(1, $mockPDO);

        // THEN you get the data you expect
        $this->assertEquals($mockSpectacleFestival['idSpectacle'], $festival['idSpectacle']);
        $this->assertEquals($mockSpectacleFestival['idFestival'], $festival['idFestival']);


    }
}
