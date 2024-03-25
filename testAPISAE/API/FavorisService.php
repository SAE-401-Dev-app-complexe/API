<?php

class FavorisService
{

    /**
     * Add a festival to the user's favorites
     * @param int $idFestival
     * @param String $apiKey
     * @param PDO $pdo
     * @return array<String>
     */
    public static function ajouterFavori(int $idFestival, String $apiKey, PDO $pdo)
    {
        $idUtilisateur = UserService::getUser($apiKey, $pdo)[0]["idUtilisateur"];
        $stmt = $pdo->prepare("INSERT IGNORE INTO festivalsfavoris (idFestival, idUtilisateur) VALUES (:idFestival, :idUtilisateur)");
        $stmt->bindParam("idFestival", $idFestival);
        $stmt->bindParam("idUtilisateur", $idUtilisateur);
        $stmt->execute();
        return array("ok"=>"ok");
    }

    /**
     * Get the user's favorite festivals
     * @param PDO $pdo
     * @param String $cleApi
     * @return array<String>
     */
   public static function getFestivalFavoris(PDO $pdo,String $cleApi): array
    {
        $idUtilisateur = UserService::getUser($cleApi, $pdo)[0]["idUtilisateur"];
        $stmt = $pdo->prepare("SELECT * FROM festival
                               INNER JOIN festivalsfavoris ON festival.idFestival = festivalsfavoris.idFestival
                               WHERE festivalsfavoris.idUtilisateur = :idUtilisateur");
        $stmt->bindParam(":idUtilisateur",$idUtilisateur);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Remove a festival from the user's favorites
     * @param String $idFestival
     * @param String $HTTP_APIKEY
     * @param PDO $getPDO
     * @return array<String>
     */
    public static function supprimerFavori(mixed $idFestival, String $HTTP_APIKEY, PDO $getPDO) : array
    {
        $idUtilisateur = UserService::getUser($HTTP_APIKEY, $getPDO)[0]["idUtilisateur"];
        $stmt = $getPDO->prepare("DELETE FROM festivalsfavoris WHERE idFestival = :idFestival AND idUtilisateur = :idUtilisateur");
        $stmt->bindParam(":idFestival",$idFestival);
        $stmt->bindParam(":idUtilisateur",$idUtilisateur);
        $stmt->execute();
        return array("ok"=>"ok");
    }
}