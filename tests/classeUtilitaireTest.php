<?php

/**
 * Classe utilitaire pour les tests
 * de l'application FestiplAndroid.
 * @author Enzo Cluzel
 * @author Lucas Descriaud
 * @author Loïc Faugières
 * @author Simon Guiraud
 */
class classeUtilitaireTest {
    
    /**
     * Insère un utilisateur dans la base de données
     */
    public static function insertUser($prenom, $nom, $mail, $login, $mdp, $pdo): void
    {
        try {
            $stmt = $pdo->prepare("INSERT INTO utilisateur (prenom, nom, mail, login, mdp)
                                   VALUES (:prenom, :nom, :mail, :login, :mdp)");
            $stmt->bindParam("login", $login);
            $stmt->bindParam("mdp", $mdp);
            $stmt->bindParam("mail", $mail);
            $stmt->bindParam("nom", $nom);
            $stmt->bindParam("prenom", $prenom);
            $stmt->execute();
        } catch (PDOException $e) {
            $e->getMessage();
        }
    }
}