<?php
class UserService {

    /**
     * Check if the user exists and give him an API key if he does
     * @param String $login
     * @param String $mdp
     * @param PDO $pdo
     * @return array<String, mixed>
     */
    public static function connection(String $login, String $mdp, PDO $pdo): array
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
     * Generate an API key for the user
     * @param PDO $pdo
     * @return string
     */
    public static function genererCleApi(PDO $pdo): string
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

            if (count($stmt->fetchAll()) != 0) {
                $cleApi = null;
            }
        } while ($cleApi == null);

        return $cleApi;
    }

    /**
     * Get the user's information
     * @param String $cleApi
     * @param PDO $pdo
     * @return array<array<String>>
     */
    public static function getUser(String $cleApi, PDO $pdo): array
    {
        $stmt = $pdo->prepare("SELECT * FROM utilisateur WHERE cleApi = :cleApi");
        $stmt-> bindParam("cleApi", $cleApi);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function verifierAuthentification(String $apiKey, PDO $getPDO): bool
    {
        $stmt = $getPDO->prepare("SELECT cleApi FROM utilisateur WHERE cleApi = :cleApi");
        $stmt-> bindParam("cleApi", $apiKey);
        $stmt->execute();
        $resultat = $stmt->fetch();
        return $resultat && $resultat["cleApi"] == $apiKey;
    }
}
