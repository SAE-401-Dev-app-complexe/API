<?php
require '../API/database.php';
require '../API/FavorisService.php';
require '../API/UserService.php';
require '../API/FestivalService.php';
require 'classeUtilitaireTest.php';

use PHPUnit\Framework\TestCase;

class FavorisServiceTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();
        // GIVEN a pdo for tests
        $this->pdo = getPDO();
    }

    public function testAjouterFavori(): void
    {
        // GIVEN the database initialized with the script in the readme
        // with a special user and a special festival
        $login = 'logintest';
        $password = 'passwordtest123';
        $idFestival = 1;
        try {
            $this->pdo->beginTransaction();
            classeUtilitaireTest::insertUser('prenomtest', 'nomtest',
                'test@mail;com', $login, $password, $this->pdo);
            $apiKey = UserService::connexion($login, $password, $this->pdo);
            $apiKey = $apiKey["cleApi"];

            // WHEN adding the festival to the user's favorites
            $result = FavorisService::ajouterFavori($idFestival, $apiKey, $this->pdo);

            // THEN the result is an array with the key 'ok' and the value 'ok'
            $this->assertEquals(['ok' => 'ok'], $result);

            $this->pdo->rollBack();

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            $this->fail("PDO error: " . $e->getMessage());
        }

    }

    public function testsupprimerFavori(): void
    {
        // GIVEN the database initialized with the script in the readme
        // with a special user and a special festival
        $login = 'logintest';
        $password = 'passwordtest123';
        $idFestival = 1;
        try {
            $this->pdo->beginTransaction();
            classeUtilitaireTest::insertUser('prenomtest', 'nomtest',
                'test@mail;com', $login, $password, $this->pdo);
            $apiKey = UserService::connexion($login, $password, $this->pdo);
            $apiKey = $apiKey["cleApi"];
            FavorisService::ajouterFavori($idFestival, $apiKey, $this->pdo);

            // WHEN removing the festival from the user's favorites
            $result = FavorisService::supprimerFavori($idFestival, $apiKey, $this->pdo);

            // THEN the result is an array with the key 'ok' and the value 'ok'
            $this->assertEquals(['ok' => 'ok'], $result);

            $this->pdo->rollBack();

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            $this->fail("PDO error: " . $e->getMessage());
        }
    }

    public function testgetFestivalFavoris(): void
    {
        //GIVEN a mock object for the PDO class, as for the
        // data you want to return
        $mockPDO = $this->createMock(PDO::class);

        $mockStmt = $this->createMock(PDOStatement::class);
        $mockPDO->method('prepare')->willReturn($mockStmt);

        $mockFavori = [
            'idFestival' => 1,
            'idUtilisateur' => 1
        ];

        $mockStmt->method('fetchAll')->willReturn($mockFavori);

        // WHEN you call the getter
        $festival = FavorisService::getFestivalFavoris($mockPDO, 'mockApiKey');

        // THEN you get the data you expect
        $this->assertEquals(1, $mockFavori['idFestival']);
        $this->assertEquals(1, $mockFavori['idUtilisateur']);
    }
}
