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

   public static function getFestivalFavoris(PDO $pdo,String $cleApi): array
    {
        $stmt = $pdo->prepare("SELECT festival.idFestival, festival.titre,festival.categorie,festival.description, festival.illustration, festival.dateDebut, festival.dateFin, 
                                CASE 
                                WHEN utilisateur.idUtilisateur IS NOT NULL THEN true
                                 ELSE false
                                END as favoris FROM festival JOIN festivalsfavoris 
                                ON festival.idFestival = festivalsfavoris.idFestival 
                                JOIN utilisateur ON festivalsfavoris.idUtilisateur = utilisateur.idUtilisateur 
                                WHERE utilisateur.cleApi = :cle");
        $stmt->bindParam(":cle",$cleApi);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}