<?php
require '../API/UserService.php';
require 'classeUtilitaireTest.php';
use PHPUnit\Framework\TestCase;

class UserServiceTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        // given a pdo for tests
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

    public function testConnexionNoError()
    {
        // given the database initialized with the script in the readme
        // and with a special user
        $login = 'logintest';
        $password = 'passwordtest123';
        try {
            $this -> pdo -> beginTransaction();
            classeUtilitaireTest::insertUser('prenomtest', 'nomtest',
                'testmail@gmail.com', $login, $password, $this->pdo);
            // when giving the login and password of the special user
            $apiKey = UserService::connection($login, $password, $this->pdo);
            // then he gets an api key that contains 20 characters
            $apiKey = $apiKey["cleApi"];
            $this->assertNotNull($apiKey);
            $this->assertEquals(20, strlen($apiKey));
            // when fetching all users
            /*
            $users = UserService::getUser($apiKey, $this->pdo);
            // then 1 and only 1 user is find
            $this->assertCount(1, $users);
            // and the user is the special user
            $this->assertEquals($login, $users[0]["login"]);
            $this->assertEquals($password, $users[0]["mdp"]);
            */
            $this -> pdo -> rollBack();
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            $this->fail("PDO error: " . $e->getMessage());
        }

    }

    public function testConnexionInvalidPassword()
    {
        // given the database initialized with the script in the readme
        // and a login that is in the database
        $login = 'logintest';
        $password = 'passwordtest123';
        $wrongPassword = 'nonpresent123';
        try {
            $this -> pdo -> beginTransaction();
            classeUtilitaireTest::insertUser('prenomtest', 'nomtest',
                'testmail@gmail.com', $login, $password, $this->pdo);
            // when giving the login and a wrong password
            $apiKey = UserService::connection($login, $wrongPassword, $this->pdo);
            // then he gets an api key that is null
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
        // given the database initialized with the script in the readme
        // and a password that is in the database
        $login = 'logintest';
        $password = 'passwordtest123';
        $wrongLogin = 'nonpresent';
        try {
            $this -> pdo -> beginTransaction();
            classeUtilitaireTest::insertUser('prenomtest', 'nomtest',
                'testmail@gmail.com', $login, $password, $this->pdo);
            // when giving a wrong login (and any password)
            $apiKey = UserService::connection($wrongLogin, $password, $this->pdo);
            // then he gets an api key that is null
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
        // given the database initialized with the script in the readme
        // and a special user
        $login = 'logintest';
        $password = 'passwordtest123';
        try {
            $this -> pdo -> beginTransaction();
            for ($i = 0; $i < 10; $i++) {
                $apiKey = UserService::genererCleApi($this->pdo);
                // then the api key contains 20 characters
                $this->assertNotNull($apiKey);
                $this->assertEquals(20, strlen($apiKey));
            }

        } catch (Exception $e) {
            $this->pdo->rollBack();
            $this->fail("Error : " . $e->getMessage());
        }
    }
    public function testGetUser()
    {
        // given the database initialized with the script in the readme
        // and a special authentified user
        $nom = 'nomtest';
        $prenom = 'prenomtest';
        try {
            $this -> pdo -> beginTransaction();
            classeUtilitaireTest::insertUser($prenom, $nom,
                'testmail@gmail.com', 'logintest', 'passwordtest123', $this->pdo);
            $apiKey = UserService::connection('logintest', 'passwordtest123', $this->pdo);

            //when fetching the user with the api key
            $user = UserService::getUser($apiKey["cleApi"], $this->pdo);
            //then the user is the special user
            $this->assertCount(1, $user);
            $this->assertEquals($nom, $user[0]["nom"]);
            $this->assertEquals($prenom, $user[0]["prenom"]);
            $this -> pdo -> rollBack();
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            $this->fail("PDO error: " . $e->getMessage());
        }
    }

    public function testDatabaseCrash()
    {
        // given a mock PDO object that throws an exception when the query method is called
        $mockPDO = $this->createMock(PDO::class);
        $mockPDO->method('prepare')->will($this->throwException(new PDOException('Database error')));
        $exceptionThrown = false;
        // when fetching all users
        try {
            $users = UserService::connection('logintest', 'passwordtest123', $mockPDO);
        } catch (PDOException $e) {
            // then a PDOException is thrown
            $this->assertEquals('Database error', $e->getMessage());
            $exceptionThrown = true;
        }

        $this->assertTrue($exceptionThrown, 'Expected PDOException was not thrown');
    }
}
