<?php

class classeUtilitaireTest {
    public static function insertUser($prenom, $nom, $mail, $login, $mdp, $pdo): void
    {
        try {
            $stmt = $pdo->prepare("INSERT INTO utilisateur (prenom, nom, mail,login, mdp ) VALUES (:prenom, :nom, :mail, :login, :mdp)");
            $stmt->bindParam("login", $login);
            $stmt->bindParam("mdp", $mdp);
            $stmt->bindParam("mail", $mail);
            $stmt->bindParam("nom", $nom);
            $stmt->bindParam("prenom", $prenom);
            $stmt->execute();
        } catch (PDOException $e) {
            $e -> getMessage();
        }

    }

    public static function insertFestival($categorie, $titre, $description, $dateDebut, $dateFin, $pdo): void
    {
        try {
            $stmt = $pdo->prepare("INSERT INTO festival (categorie, titre, description, dateDebut, dateFin) VALUES (:categorie, :titre, :description, :dateDebut, :dateFin)");
            $stmt->bindParam("categorie", $categorie);
            $stmt->bindParam("titre", $titre);
            $stmt->bindParam("description", $description);
            $stmt->bindParam("dateDebut", $dateDebut);
            $stmt->bindParam("dateFin", $dateFin);
            $stmt->execute();
        } catch (PDOException $e) {
            $e -> getMessage();
        }

    }

    public static function deleteAllFestivals($pdo): void
    {
        try {
            $stmt = $pdo->prepare("DELETE FROM festival");
            $stmt->execute();
        } catch (PDOException $e) {
            $e -> getMessage();
        }

    }
    public static function insertFavoris($idFestival, $idUtilisateur, $pdo): void
    {
        try {
            $stmt = $pdo->prepare("INSERT INTO festivalsfavoris (idFestival, idUtilisateur) VALUES (:idFestival, :idUtilisateur)");
            $stmt->bindParam("idFestival", $idFestival);
            $stmt->bindParam("idUtilisateur", $idUtilisateur);
            $stmt->execute();
        } catch (PDOException $e) {
            $e -> getMessage();
        }

    }
}