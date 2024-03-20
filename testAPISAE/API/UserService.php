<?php
class UserService {

    public static function connection($login, $mdp, $pdo): array
    {
        $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE login = :login AND mdp = :mdp");
        $stmt-> bindParam("login", $login);
        $stmt-> bindParam("mdp", $mdp);
        $stmt->execute();
        $result = $stmt->fetchAll();

        if (count($result) > 0) {
            if ($result[0]["cleApi"] == null) {
                $cleGeneree = UserService::genererCleApi($login, $pdo);
                return array("cleApi" => $cleGeneree);
            }
            return array("cleApi" => $result[0]["cleApi"]);
        } else {
            return array("cleApi" => null);
        }
    }

    public static function genererCleApi($login, $pdo): string
    {
        // Génère une clé API aléatoire de 20 caractères alphanumériques
        // puis modifie l'utilisateur et lui associe cette clé
        // attention : vérifier qu'un utilisateur ayant la même clé n'existe pas
        $cleApi = null;
        do {
            $cleApi = bin2hex(random_bytes(10));


            $stmt = $pdo->prepare("SELECT cleApi FROM utilisateur WHERE cleApi = :cleApi");
            $stmt-> bindParam("cleApi", $cleApi);
            $stmt->execute();

            if (count($stmt->fetchAll()) == 0) {
                $stmt = $pdo->prepare("UPDATE utilisateur SET cleApi = :cleApi WHERE login = :login");
                $stmt-> bindParam("cleApi", $cleApi);
                $stmt-> bindParam("login", $login);
                $stmt->execute();
            } else {
                $cleApi = null;
            }
        } while ($cleApi == null);

        return $cleApi;
    }

    public static function getUser($cleApi, $pdo): array
    {
        $stmt = $pdo->prepare("SELECT nom, prenom FROM utilisateur WHERE cleApi = :cleApi");
        $stmt-> bindParam("cleApi" , $cleApi);
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
