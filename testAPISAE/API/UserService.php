<?php
class UserService {

    public static function connection($login , $mdp , $pdo): array
    {
        $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE login = :login AND mdp = :mdp");
        $stmt-> bindParam("login" , $login);
        $stmt-> bindParam("mdp" , $mdp);
        $stmt->execute();
        return count($stmt->fetchAll()) > 0 ? array("cleApi" => "testApiKey") : array("cleApi" => null);
    }
}
