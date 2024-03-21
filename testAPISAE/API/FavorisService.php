<?php

class FavorisService
{

    public static function ajouterFavoris(int $idFestival, int $idUtilisateur, PDO $pdo): array
    {
        $stmt = $pdo->prepare("INSERT INTO festivalsfavoris (idFestival, idUtilisateur) VALUES (:idFestival, :idUtilisateur)");
        $stmt->bindParam("idFestival", $idFestival);
        $stmt->bindParam("idUtilisateur", $idUtilisateur);
        $stmt->execute();
    }

    public static function getFavoris(String $apiKey, PDO $getPDO)
    {
        $stmt = $getPDO->prepare("SELECT FA.* FROM festivalsfavoris FA INNER JOIN utilisateur UT ON FA.idUtilisateur = UT.idUtilisateur WHERE UT.cleApi = :APIKEY");
        $stmt->bindParam("APIKEY", $apiKey);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}