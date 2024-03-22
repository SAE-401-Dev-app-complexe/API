<?php

class FavorisService
{

    public static function ajouterFavoris(int $idFestival, int $idUtilisateur, PDO $pdo)
    {
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
}