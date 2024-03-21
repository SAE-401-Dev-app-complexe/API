<?php

class FestivalService {

    public static function getFestival($pdo,$cle): array
    {
        
        $stmt = $pdo->prepare("SELECT festival.idFestival, festival.titre,festival.categorie, festival.illustration, festival.description, festival.dateDebut, festival.dateFin, 
        CASE 
        WHEN utilisateur.idUtilisateur IS NOT NULL THEN true
        ELSE false
        END as favoris
        FROM festival
        LEFT JOIN festivalsfavoris ON festival.idFestival = festivalsfavoris.idFestival
        LEFT JOIN utilisateur ON festivalsfavoris.idUtilisateur = utilisateur.idUtilisateur
        WHERE festival.dateFin > NOW() 
        AND (utilisateur.cleApi = :cleApi OR festivalsfavoris.idFestival IS NULL)");
        $stmt->bindParam("cleApi",$cle);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
