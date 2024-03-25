<?php

class FestivalService {

    /**
     * Get all festivals starting from today
     * @param PDO $pdo
     * @param String $cleApi
     * @return array<String>
     */
    public static function getFestival(PDO $pdo, String $cleApi): array
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

        $stmt->bindParam("cleApi",$cleApi);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * @param mixed $idFestival id a chercher
     * @param PDO $getPDO pdo de la bd
     * @return array<String>
     * @throws PDOException
     */
    public static function getDetailsFestival(mixed $idFestival, PDO $getPDO) : array
    {
        $stmt = $getPDO->prepare("SELECT SP.* FROM spectacledefestival SDF
                                                                INNER JOIN spectacle SP ON SDF.idSpectacle = SP.idSpectacle
                                                                WHERE SDF.idFestival = :idFestival");
        $stmt->bindParam(":idFestival",$idFestival);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
