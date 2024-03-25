<?php
//Import des fichiers utiles pour les fonctions
require 'bd.php';
require 'UserService.php';
require 'FestivalService.php';
require 'FavorisService.php';

function sendJson(array $jsonData, int $code = 200) : void
{
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');

    http_response_code($code);
    echo json_encode($jsonData);
}

/**
 * @param String $error message decrivant l'erreur
 * @param int $code code de l'erreur
 * @param String $details explique précisement les causes de l'erreur
 * @return array tableau renvoyé contenant toutes les informations de l'erreur
 */
function getErrorArray(String $error, int $code, String $details) : array
{
    return [
        'error' => $error,
        'code' => $code,
        'details' => $details
    ];
}

/**
 * @return bool renvoie si la clé utilisée dans le header requête existe dans la base de données ou non.
 */
function verifierAuthentification() : bool
{
    if (!isset($_SERVER['HTTP_APIKEY']) || !UserService::verifierAuthentification($_SERVER['HTTP_APIKEY'], getPDO())) {
        sendJson(getErrorArray('Accès non autorisé', 401, 'Veuillez fournir une clé API valide'), 401);
        return false;
    }
    return true;
}

// Suite de l'url après le /API/
$demande = !empty($_GET['demande'])
    ? htmlspecialchars($_GET['demande'])
    : null;

// Nom de la méthode a appeler dans le switch.
$ressource = $demande ?
    explode('/', filter_var($demande, FILTER_SANITIZE_URL))[0]
    : null;

// Données dans le corps de la requête
$donnees = json_decode(file_get_contents('php://input'), true);


switch ($ressource)
{
    // Cas ou la méthode n'est pas spécifié
    case null:
        sendJson(getErrorArray('Mauvaise requête', 400, 'Spécifiez la ressource à envoyer'), 400);
        break;
    // Verifie si l'utilisateur existe et lui donne si c'est le cas une clé API
    case 'authentification':
        try {
            $login = $donnees['login'] ?? null;
            $password = $donnees['password'] ?? null;
            if (!$login || !$password) {
                sendJson(getErrorArray('Mauvaise requête', 400, 'Identifiant ou mot de passe manquant'), 400);
                break;
            }
            sendJson(UserService::connection($login, $password, getPDO()));
        } catch (PDOException $e) {
            sendJson(getErrorArray('Erreur interne au serveur', 500, $e), 500);
        }
        break;
    // Obtention des informations de tous les festivals commençant a partir d'aujourd'hui
    case 'festivals':
        try {
            sendJson(FestivalService::getFestival(getPDO(), $_SERVER['HTTP_APIKEY']));
        } catch (PDOException $e) {
            sendJson(getErrorArray('Erreur interne au serveur', 500, $e), 500);
        }
        break;
    // Ajoute un festival en favoris a l'utilisateur de l'api
    case 'ajouterFavori':
        try {
            if (verifierAuthentification()) {
                $idFestival = $donnees['idFestival'] ?? null;
                if (!$idFestival) {
                    sendJson(getErrorArray('Mauvaise requête', 400, 'ID du festival à mettre en favori manquant'), 400);
                    break;
                }
                sendJson(FavorisService::ajouterFavori($idFestival, $_SERVER['HTTP_APIKEY'], getPDO()));
            }
        } catch (PDOException $e) {
            sendJson(getErrorArray('Erreur interne au serveur', 500, $e), 500);
        }
        break;
    // Renvoie la liste de tous les festivals en favoris de l'utilisateur
    case 'favoris':
        try {
            if (verifierAuthentification()) {
                sendJson(FavorisService::getFestivalFavoris(getPDO(), $_SERVER['HTTP_APIKEY']));
            }
        } catch (PDOException $e) {
            sendJson(getErrorArray('Erreur interne au serveur', 500, $e), 500);
        }
        break;
    // Supprime un festival de la liste des favoris de l'utilisateur
    case 'supprimerFavori':
        try {
            if (verifierAuthentification()) {
                $idFestival = $donnees['idFestival'] ?? null;
                if (!$idFestival) {
                    sendJson(getErrorArray('Mauvaise requête', 400, 'ID du festival à supprimer manquant'), 400);
                    break;
                }
                sendJson(FavorisService::supprimerFavori($idFestival, $_SERVER['HTTP_APIKEY'], getPDO()));
            }
        } catch (PDOException $e) {
            sendJson(getErrorArray('Erreur interne au serveur', 500, $e), 500);
        }
        break;
    // tentative d'appel d'une méthode n'existant pas
    default:
        sendJson(getErrorArray('URL non trouvée', 404, 'Requête inconnue'), 404);
        break;
}       
