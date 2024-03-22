<?php
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

function getErrorArray(String $error, int $code, String $details) : array
{
    return [
        'error' => $error,
        'code' => $code,
        'details' => $details
    ];
}

function verifierAuthentification() : bool
{
    if (!isset($_SERVER['HTTP_APIKEY']) || !UserService::verifierAuthentification($_SERVER['HTTP_APIKEY'], getPDO())) {
        sendJson(getErrorArray('Accès non autorisé', 401, 'Veuillez fournir une clé API valide'), 401);
        return false;
    }
    return true;
}

$demande = !empty($_GET['demande'])
    ? htmlspecialchars($_GET['demande'])
    : null;

$ressource = $demande ?
    explode('/', filter_var($demande, FILTER_SANITIZE_URL))[0]
    : null;

$id = $demande ?
    explode('/', $demande)[1] ?? null
    : null;

$donnees = json_decode(file_get_contents('php://input'), true);

switch ($ressource)
{
    case null:
        sendJson(getErrorArray('Mauvaise requête', 400, 'Spécifiez la ressource à envoyer'), 400);
        break;

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

    case 'festivals':
        try {
            sendJson(FestivalService::getFestival(getPDO(), $_SERVER['HTTP_APIKEY']));
        } catch (PDOException $e) {
            sendJson(getErrorArray('Erreur interne au serveur', 500, $e), 500);
        }
        break;
    case 'ajouterFavori':
        try {
            if (verifierAuthentification()) {
                $idFestival = $donnees['idFestival'] ?? null;
                if (!$idFestival) {
                    sendJson(getErrorArray('Mauvaise requête', 400, 'ID du festival à mettre en favori manquant'), 400);
                    break;
                }
                sendJson(FavorisService::ajouterFavoris($idFestival, $_SERVER['HTTP_APIKEY'], getPDO()), $_SERVER['HTTP_APIKEY']);
            }
        } catch (PDOException $e) {
            sendJson(getErrorArray('Erreur interne au serveur', 500, $e), 500);
        }
        break;
    case 'favoris':
        try {
            if (verifierAuthentification()) {
                sendJson(FavorisService::getFestivalFavoris(getPDO(), $_SERVER['HTTP_APIKEY']));
            }
        } catch (PDOException $e) {
            sendJson(getErrorArray('Erreur interne au serveur', 500, $e), 500);
        }
        break;
    case 'supprimerFavori':
        try {
            if (verifierAuthentification()) {
                $idFestival = $donnees['idFestival'] ?? null;
                if (!$idFestival) {
                    sendJson(getErrorArray('Mauvaise requête', 400, 'ID du festival à supprimer manquant'), 400);
                    break;
                }
                sendJson(FestivalService::supprimerFavoris($idFestival, $_SERVER['HTTP_APIKEY'], getPDO()), $_SERVER['HTTP_APIKEY']);
            }
        } catch (PDOException $e) {
            sendJson(getErrorArray('Erreur interne au serveur', 500, $e), 500);
        }
        break;
    default:
        sendJson(getErrorArray('URL non trouvée', 404, 'Requête inconnue'), 404);
        break;

}       
