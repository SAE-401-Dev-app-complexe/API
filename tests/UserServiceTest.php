<?php
require '../API/UserService.php';
require 'classeUtilitaireTest.php';
require '../API/database.php';

use PHPUnit\Framework\TestCase;

class UserServiceTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        // GIVEN a pdo for tests
        $this->pdo = getPDO();
    }

    public function testConnexionNoError()
    {
        // GIVEN the database initialized with the script in the readme
        // and with a special user
        $login = 'logintest';
        $password = 'passwordtest123';
        try {
            $this -> pdo -> beginTransaction();
            classeUtilitaireTest::insertUser('prenomtest', 'nomtest',
                'testmail@gmail.com', $login, $password, $this->pdo);
            // WHEN giving the login and password of the special user
            $apiKey = UserService::connexion($login, $password, $this->pdo);
            // THEN he gets an api key that contains 20 characters
            $apiKey = $apiKey["cleApi"];
            $this->assertNotNull($apiKey);
            $this->assertEquals(20, strlen($apiKey));
            $this -> pdo -> rollBack();
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            $this->fail("PDO error: " . $e->getMessage());
        }

    }

    public function testConnexionInvalidPassword()
    {
        // GIVEN the database initialized with the script in the readme
        // and a login that is in the database
        $login = 'logintest';
        $password = 'passwordtest123';
        $wrongPassword = 'nonpresent123';
        try {
            $this -> pdo -> beginTransaction();
            classeUtilitaireTest::insertUser('prenomtest', 'nomtest',
                'testmail@gmail.com', $login, $password, $this->pdo);
            // WHEN giving the login and a wrong password
            $apiKey = UserService::connexion($login, $wrongPassword, $this->pdo);
            // THEN he gets an api key that is null
            $apiKey = $apiKey["cleApi"];
            $this->assertNull($apiKey);
            $this -> pdo -> rollBack();
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            $this->fail("PDO error: " . $e->getMessage());
        }
    }


    public function testConnexionInvalidLogin()
    {
        // GIVEN the database initialized with the script in the readme
        // and a password that is in the database
        $login = 'logintest';
        $password = 'passwordtest123';
        $wrongLogin = 'nonpresent';
        try {
            $this -> pdo -> beginTransaction();
            classeUtilitaireTest::insertUser('prenomtest', 'nomtest',
                'testmail@gmail.com', $login, $password, $this->pdo);
            // WHEN giving a wrong login (and any password)
            $apiKey = UserService::connexion($wrongLogin, $password, $this->pdo);
            // THEN he gets an api key that is null
            $apiKey = $apiKey["cleApi"];
            $this->assertNull($apiKey);
            $this -> pdo -> rollBack();
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            $this->fail("PDO error: " . $e->getMessage());
        }
    }

    public function testGenererCleApi()
    {
        // GIVEN the database initialized with the script in the readme
        // and a special user
        try {
            $this -> pdo -> beginTransaction();
            for ($i = 0; $i < 10; $i++) {
                $apiKey = UserService::genererCleApi($this->pdo);
                // THEN the api key contains 20 characters
                $this->assertNotNull($apiKey);
                $this->assertEquals(20, strlen($apiKey));
            }

        } catch (Exception $e) {
            $this->pdo->rollBack();
            $this->fail("Error : " . $e->getMessage());
        }
    }

    public function testGetUtilisateurNoError()
    {
        // GIVEN the database initialized with the script in the readme
        // and a special authentified user
        $nom = 'nomtest';
        $prenom = 'prenomtest';
        try {
            $this -> pdo -> beginTransaction();
            classeUtilitaireTest::insertUser($prenom, $nom,
                'testmail@gmail.com', 'logintest', 'passwordtest123', $this->pdo);
            $apiKey = UserService::connexion('logintest', 'passwordtest123', $this->pdo);

            //WHEN fetching the user with the api key
            $user = UserService::getUtilisateur($apiKey["cleApi"], $this->pdo);
            //THEN the user is the special user
            $this->assertCount(1, $user);
            $this->assertEquals($nom, $user[0]["nom"]);
            $this->assertEquals($prenom, $user[0]["prenom"]);
            $this -> pdo -> rollBack();
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            $this->fail("PDO error: " . $e->getMessage());
        }
    }

    public function testGetUtilisateurInvalidApiKey()
    {
        // GIVEN the database initialized with the script in the readme
        // and a false apiKey
        $invalidApiKey = 'azertyuiopmlkjhgfdsq';
        try {
            $this -> pdo -> beginTransaction();
            //WHEN fetching an user with the api key
            $user = UserService::getUtilisateur($invalidApiKey, $this->pdo);
            //THEN there is no user
            $this->assertCount(0, $user);
            $this -> pdo -> rollBack();
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            $this->fail("PDO error: " . $e->getMessage());
        }
    }

    public  function testVerifierAuthentificationNoError()
    {
        // GIVEN the database initialized with the script in the readme
        // and a special user
        $login = 'logintest';
        $password = 'passwordtest123';
        try {
            $this->pdo->beginTransaction();
            classeUtilitaireTest::insertUser('prenomtest', 'nomtest',
                'testmail@gmail.com', $login, $password, $this->pdo);
            $apiKey = UserService::connexion($login, $password, $this->pdo);
            // WHEN verifying the authentification with the api key of the user
            $authentification = UserService::verifierAuthentification($apiKey["cleApi"], $this->pdo);
            // THEN the authentification is true
            $this->assertTrue($authentification);
            $this->pdo->rollBack();

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            $this->fail("PDO error: " . $e->getMessage());
        }
    }

    public function testVerifierAuthentificationInvalidApiKey()
    {
        // GIVEN the database initialized with the script in the readme
        // and a special user
        $login = 'logintest';
        $password = 'passwordtest123';
        try {
            $this->pdo->beginTransaction();
            classeUtilitaireTest::insertUser('prenomtest', 'nomtest',
                'testmail@gmail.com', $login, $password, $this->pdo);
            $apiKey = UserService::connexion($login, $password, $this->pdo);
            // WHEN verifying the authentification with an api random api key
            $authentification = UserService::verifierAuthentification(UserService::genererCleApi($this->pdo), $this->pdo);
            // THEN the authentification is false
            $this->assertFalse($authentification);
            $this->pdo->rollBack();

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            $this->fail("PDO error: " . $e->getMessage());
        }
    }


    public function testDatabaseCrash()
    {
        // GIVEN a mock PDO object that throws an exception when the query method is called
        $mockPDO = $this->createMock(PDO::class);
        $mockPDO->method('prepare')->will($this->throwException(new PDOException('Database error')));
        $exceptionThrown = false;
        // WHEN fetching all users
        try {
            $users = UserService::connexion('logintest', 'passwordtest123', $mockPDO);
        } catch (PDOException $e) {
            // THEN a PDOException is thrown
            $this->assertEquals('Database error', $e->getMessage());
            $exceptionThrown = true;
        }

        $this->assertTrue($exceptionThrown, 'Expected PDOException was not thrown');
    }
}
