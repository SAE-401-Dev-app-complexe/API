<?php

class FavorisService
{

    public static function ajouterFavoris(int $idFestival, String $apiKey, PDO $pdo)
    {
        $idUtilisateur = UserService::getUser($apiKey, $pdo)[0]["idUtilisateur"];
        $stmt = $pdo->prepare("INSERT INTO festivalsfavoris (idFestival, idUtilisateur) VALUES (:idFestival, :idUtilisateur)");
        $stmt->bindParam("idFestival", $idFestival);
        $stmt->bindParam("idUtilisateur", $idUtilisateur);
        $stmt->execute();
    }

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

    public static function supprimerFavoris(mixed $idFestival, mixed $HTTP_APIKEY, PDO $getPDO)
    {
        $idUtilisateur = UserService::getUser($HTTP_APIKEY, $getPDO)[0]["idUtilisateur"];
        $stmt = $getPDO->prepare("DELETE FROM festivalsfavoris WHERE idFestival = :idFestival AND idUtilisateur = :idUtilisateur");
        $stmt->bindParam(":idFestival",$idFestival);
        $stmt->bindParam(":idUtilisateur",$idUtilisateur);
        $stmt->execute();
    }
}