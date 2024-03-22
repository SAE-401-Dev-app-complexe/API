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
        sendJson(getErrorArray('Acces non autorisé', 401, 'Vous ne possedez pas de clé API'), 401);
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
        sendJson(getErrorArray('Mauvaise requete', 400, 'Pas de fonction spécifié'), 400);
        break;

    case 'authentification':
        try {
            $login = $donnees['login'] ?? null;
            $password = $donnees['password'] ?? null;
            if (!$login || !$password) {
                sendJson(getErrorArray('Mauvaise requete', 400, 'Identifiant ou Mot de passe manquant'), 400);
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
    case 'ajouterFavoris':
        try {
            if (verifierAuthentification()) {
                $idFestival = $donnees['idFestival'] ?? null;
                if (!$idFestival) {
                    sendJson(getErrorArray('Mauvaise requete', 400, 'ID du festival a mettre en favoris manquant'), 400);
                    break;
                }
                FavorisService::ajouterFavoris($idFestival, $_SERVER['HTTP_APIKEY'], getPDO());
            }
        } catch (PDOException $e) {
            sendJson(getErrorArray('Erreur interne au serveur', 500, $e), 500);
        }
        break;
    case 'favoris':
        try {
            if (verifierAuthentification()) {
                sendJson(FavorisService::getFestivalFavoris(getPDO() , $_SERVER['HTTP_APIKEY']));
            }
        } catch (PDOException $e) {
            sendJson(getErrorArray('Erreur interne au serveur', 500, $e), 500);
        }
        break;
    case 'supprimerFavoris':
        try {
            if (verifierAuthentification()) {
                $idFestival = $donnees['idFestival'] ?? null;
                if (!$idFestival) {
                    sendJson(getErrorArray('Mauvaise requete', 400, 'ID du festival a supprimer manquant '), 400);
                    break;
                }
                FavorisService::supprimerFavoris($idFestival, $_SERVER['HTTP_APIKEY'], getPDO());
            }
        } catch (PDOException $e) {
            sendJson(getErrorArray('Erreur interne au serveur', 500, $e), 500);
        }
        break;
    default:
        sendJson(getErrorArray('URL non trouvé', 404, 'requete inconnue'), 404);
        break;

}       
