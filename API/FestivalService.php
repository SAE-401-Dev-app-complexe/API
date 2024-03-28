<?php

/**
 * Service gérant les festivals créés sur FestiplAndroid.
 * @author Enzo Cluzel
 * @author Lucas Descriaud
 * @author Loïc Faugières
 * @author Simon Guiraud
 */
class FestivalService {

    /**
     * Récupère la liste des festivals à venir.
     * @param PDO $pdo La connexion à la base de données
     * @param String $cleApi La clé api de l'utilisateur
     * @return array<String> La liste des festivals à venir
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

        $stmt->bindParam("cleApi", $cleApi);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Récupère les spectacles d'un festival.
     * @param mixed $idFestival L'id du festival
     * @param PDO $getPDO La connexion à la base de données
     * @return array<String> La liste des spectacles du festival
     */
    public static function getDetailsFestival(mixed $idFestival, PDO $getPDO): array
    {
        $stmt = $getPDO->prepare("SELECT SP.* FROM spectacledefestival SDF
                                  INNER JOIN spectacle SP
                                  ON SDF.idSpectacle = SP.idSpectacle
                                  WHERE SDF.idFestival = :idFestival");

        $stmt->bindParam(":idFestival", $idFestival);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
