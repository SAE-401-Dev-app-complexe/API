<?php

class FavorisService
{

    public static function ajouterFavoris(int $idFestival, int $idUtilisateur , PDO $pdo): array
    {
        $stmt = $pdo->prepare("INSERT INTO favoris (idFestival , idUtilisateur) VALUES (:idFestival, :idUtilisateur)");
        $stmt->bindParam("idFestival", $idFestival);
        $stmt->bindParam("idUtilisateur", $idUtilisateur);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}