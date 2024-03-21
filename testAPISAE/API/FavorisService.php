<?php

class FavorisService
{

    public static function ajouterFavoris(int $idFestival, int $idUtilisateur , PDO $pdo): array
    {
        $stmt = $pdo->prepare("INSERT INTO festivalsfavoris (idFestival , idUtilisateur) VALUES (:idFestival, :idUtilisateur)");
        $stmt->bindParam("idFestival", $idFestival);
        $stmt->bindParam("idUtilisateur", $idUtilisateur);
        $stmt->execute();
    }

    public static function getFavoris(int $idUtilisateur, PDO $getPDO)
    {
        $stmt = $getPDO->prepare("SELECT * FROM festivalsfavoris WHERE idUtilisateur = :idUtilisateur");
        $stmt->bindParam("idUtilisateur", $idUtilisateur);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}