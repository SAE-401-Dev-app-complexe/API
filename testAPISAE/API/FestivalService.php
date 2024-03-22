<?php

class FestivalService {

    public static function getFestival(PDO $pdo, String $cle): array
    {
        $stmt = $pdo->prepare("
            SELECT FE.idFestival, FE.titre, FE.illustration, FE.description, DATE_FORMAT(FE.dateDebut, '%d/%m/%Y') as dateDebut, DATE_FORMAT(FE.dateFin, '%d/%m/%Y') as dateFin,
            CASE 
            WHEN UT.idUtilisateur IS NOT NULL THEN true
            ELSE false
            END as favoris
            FROM festival FE
            LEFT JOIN festivalsfavoris FF
            ON FE.idFestival = FF.idFestival
            LEFT JOIN utilisateur UT
            ON FF.idUtilisateur = UT.idUtilisateur
            WHERE FE.dateDebut >= NOW() 
            AND (UT.cleApi = :cleApi OR FF.idFestival IS NULL)
            ORDER BY FE.dateDebut ASC;");

        $stmt->bindParam("cleApi",$cle);
        $stmt->execute();
        $result = $stmt->fetchAll();

    }
}
