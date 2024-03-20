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

    public static function inscription($prenom, $nom, $mail, $login, $mdp, $pdo): array
    {
        $stmt = $pdo->prepare("INSERT INTO utilisateur (prenom , nom, mail,login, mdp ) VALUES (:prenom, :nom, :mail, :login, :mdp)");
        $stmt->bindParam("login", $login);
        $stmt->bindParam("mdp", $mdp);
        $stmt->bindParam("mail", $mail);
        $stmt->bindParam("nom", $nom);
        $stmt->bindParam("prenom", $prenom);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function getUser($cleApi, $pdo): array
    {
        $stmt = $pdo->prepare("SELECT nom, prenom FROM utilisateur WHERE cleApi = :cleApi");
        $stmt-> bindParam("cleApi" , $cleApi);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
