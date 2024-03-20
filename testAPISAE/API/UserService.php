<?php
class UserService {

    public static function connection($login , $mdp , $pdo): array
    {
            $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE login = :login AND mdp = :mdp");
            $stmt-> bindParam("login" , $login);
            $stmt-> bindParam("mdp" , $mdp);
            $stmt->execute();
            return $stmt->fetchAll();
    }

    public static function verifierAuthentification(int $idUtilisateur, mixed $HTTP_APIKEY, PDO $getPDO)
    {
$stmt = $getPDO->prepare("SELECT cleApi FROM utilisateur WHERE idUtilisateur = :id ");
        $stmt-> bindParam("id" , $idUtilisateur);
        $stmt-> bindParam("apikey" , $HTTP_APIKEY);
        $stmt->execute();
        $resultat = $stmt->fetchAll();
        return $resultat == $HTTP_APIKEY;
    }
}
