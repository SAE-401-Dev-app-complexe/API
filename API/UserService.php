<?php

/**
 * Service gérant l'authentification des utilisateurs
 * de l'application FestiplAndroid.
 * @author Enzo Cluzel
 * @author Lucas Descriaud
 * @author Loïc Faugières
 * @author Simon Guiraud
 */
class UserService {

    /**
     * Vérifie les identifiants de l'utilisateur et génère une clé API si besoin
     * @param String $login Le login de l'utilisateur
     * @param String $mdp Le mot de passe de l'utilisateur
     * @param PDO $pdo La connexion à la base de données
     * @return array<String, mixed> La clé API de l'utilisateur
     */
    public static function connexion(String $login, String $mdp, PDO $pdo): array
    {
        $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE login = :login AND mdp = :mdp");
        $stmt-> bindParam("login", $login);
        $stmt-> bindParam("mdp", $mdp);
        
        $stmt->execute();
        $result = $stmt->fetchAll();

        if (count($result) > 0) {
            if ($result[0]["cleApi"] == null) {
                $cleGeneree = UserService::genererCleApi($pdo);

                $stmt = $pdo->prepare("UPDATE utilisateur SET cleApi = :cleApi WHERE login = :login");
                $stmt-> bindParam("cleApi", $cleGeneree);
                $stmt-> bindParam("login", $login);
                $stmt->execute();
                return array("cleApi" => $cleGeneree);
            }
            return array("cleApi" => $result[0]["cleApi"]);
        } else {
            return array("cleApi" => null);
        }
    }

    /**
     * Génère une clé API aléatoire de 20 caractères alphanumériques
     * en vérifiant la non existence de cette clé dans la base de données
     * @param PDO $pdo La connexion à la base de données
     * @return string La clé API générée
     */
    public static function genererCleApi(PDO $pdo): string
    {
        $cleApi = null;
        do {
            $cleApi = bin2hex(random_bytes(10));

            $stmt = $pdo->prepare("SELECT cleApi FROM utilisateur WHERE cleApi = :cleApi");
            $stmt-> bindParam("cleApi", $cleApi);
            $stmt->execute();

            if (count($stmt->fetchAll()) != 0) {
                $cleApi = null;
            }
        } while ($cleApi == null);

        return $cleApi;
    }

    /**
     * 
     * @param String $cleApi
     * @param PDO $pdo
     * @return array<array<String>>
     */
    public static function getUtilisateur(String $cleApi, PDO $pdo): array
    {
        $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE cleApi = :cleApi");
        $stmt-> bindParam("cleApi", $cleApi);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Vérifie si une clé API est présente dans la base de données
     * @param String $apiKey la clé API à vérifier
     * @param PDO $getPDO La connexion à la base de données
     * @return bool true si la clé API est présente, false sinon
     */
    public static function verifierAuthentification(String $apiKey, PDO $getPDO): bool
    {
        $stmt = $getPDO->prepare("SELECT cleApi FROM utilisateur WHERE cleApi = :cleApi");
        $stmt-> bindParam("cleApi", $apiKey);
        $stmt->execute();
        $resultat = $stmt->fetch();
        return $resultat && $resultat["cleApi"] == $apiKey;
    }
}
