<?php

class FestivalService {

    public static function getFestival(PDO $pdo,String $cle): array
    {
        
        $stmt = $pdo->prepare("
        SELECT festival.idFestival, festival.titre,festival.categorie, festival.illustration, festival.description, DATE_FORMAT(festival.dateDebut, '%d/%m/%Y'), DATE_FORMAT(festival.dateFin, '%d/%m/%Y'), 
        CASE 
        WHEN utilisateur.idUtilisateur IS NOT NULL THEN true
        ELSE false
        END as favoris
        FROM festival
        LEFT JOIN festivalsfavoris ON festival.idFestival = festivalsfavoris.idFestival
        LEFT JOIN utilisateur ON festivalsfavoris.idUtilisateur = utilisateur.idUtilisateur
        WHERE festival.dateDebut >= NOW() 
        AND (utilisateur.cleApi = :cleApi OR festivalsfavoris.idFestival IS NULL)
        ORDER BY festival.dateDebut ASC");
        $stmt->bindParam("cleApi",$cle);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
