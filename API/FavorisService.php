<?php

/**
 * Service gérant les favoris des utilisateurs
 * de l'application FestiplAndroid.
 * @author Enzo Cluzel
 * @author Lucas Descriaud
 * @author Loïc Faugières
 * @author Simon Guiraud
 */
class FavorisService
{

    /**
     * Récupère la liste des festivals favoris d'un utilisateur.
     * @param PDO $pdo La connexion à la base de données
     * @param String $cleApi La clé api de l'utilisateur
     * @return array<String> La liste des festivals favoris de l'utilisateur
     */
    public static function getFestivalFavoris(PDO $pdo, String $cleApi): array
    {
        $idUtilisateur = UserService::getUtilisateur($cleApi, $pdo)[0]["idUtilisateur"];
        $stmt = $pdo->prepare("SELECT * FROM festival
                               INNER JOIN festivalsfavoris ON festival.idFestival = festivalsfavoris.idFestival
                               WHERE festivalsfavoris.idUtilisateur = :idUtilisateur");
        $stmt->bindParam(":idUtilisateur",$idUtilisateur);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Ajoute un festival aux favoris d'un utilisateur.
     * @param int $idFestival L'id du festival à ajouter
     * @param String $apiKey La clé api de l'utilisateur
     * @param PDO $pdo La connexion à la base de données
     * @return array<String> Un tableau associatif contenant la clé "ok"
     */
    public static function ajouterFavori(int $idFestival, String $apiKey, PDO $pdo): array
    {
        $idUtilisateur = UserService::getUtilisateur($apiKey, $pdo)[0]["idUtilisateur"];
        $stmt = $pdo->prepare("INSERT IGNORE INTO festivalsfavoris (idFestival, idUtilisateur) VALUES (:idFestival, :idUtilisateur)");
        $stmt->bindParam("idFestival", $idFestival);
        $stmt->bindParam("idUtilisateur", $idUtilisateur);
        $stmt->execute();
        return array("ok" => "ok");
    }

    /**
     * Supprime un festival des favoris d'un utilisateur.
     * @param String $idFestival L'id du festival à supprimer
     * @param String $HTTP_APIKEY La clé api de l'utilisateur
     * @param PDO $getPDO La connexion à la base de données
     * @return array<String> Un tableau associatif contenant la clé "ok"
     */
    public static function supprimerFavori(mixed $idFestival, String $HTTP_APIKEY, PDO $getPDO) : array
    {
        $idUtilisateur = UserService::getUtilisateur($HTTP_APIKEY, $getPDO)[0]["idUtilisateur"];
        $stmt = $getPDO->prepare("DELETE FROM festivalsfavoris WHERE idFestival = :idFestival AND idUtilisateur = :idUtilisateur");
        $stmt->bindParam(":idFestival",$idFestival);
        $stmt->bindParam(":idUtilisateur",$idUtilisateur);
        $stmt->execute();
        return array("ok" => "ok");
    }
}