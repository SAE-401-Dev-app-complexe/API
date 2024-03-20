<?php
require 'bd.php';
require 'UserService.php';
require 'FestivalService.php';
require 'FavorisService.php';
function sendJson($jsonData, $code = 200)
{
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
  
    http_response_code($code);

    echo json_encode($jsonData);
}

function getErrorArray($error, $code, $details)
{
    return [
        'error' => $error,
        'code' => $code,
        'details' => $details
    ];
}

function verifierAuthentification()
{
    $idUtilisateur = 1; //STUB
    if (!isset($_SERVER['HTTP_APIKEY']) || !UserService::verifierAuthentification($idUtilisateur , $_SERVER['HTTP_APIKEY'], getPDO())) {
        sendJson(getErrorArray('Unauthorized', 401, 'Unauthorized access'), 401);
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
        sendJson(getErrorArray('Bad request', 400, 'No request specified'), 400);
        break;

    case 'authentification':
        try {
            //TODO recuperer login password
            //$login = $password = "non"; //STUB           il suffit de lire le cours de M. Rous qui indique comment récupérer les données du body de l'appel
            // récupérer les données de la requête         je vous le fais, ça me bloque pour mes tests
            $login = $donnees['login'] ?? null;
            $password = $donnees['password'] ?? null;
            if (!$login || !$password) {
                sendJson(getErrorArray('Bad request', 400, 'Missing login or password'), 400);
                break;
            }
            sendJson(UserService::connection($login, $password, getPDO()));
        } catch (PDOException $e) {
            sendJson(getErrorArray('Internal server error', 500, $e), 500);
        }
        break;

    case 'festival':
        try {
            sendJson(FestivalService::getFestival(getPDO()));
        } catch (PDOException $e) {
            sendJson(getErrorArray('Internal server error', 500, $e), 500);
        }
        break;
    case 'ajouterFavoris':
        try {
            if (verifierAuthentification()) {

                //TODO recuperer idFestival/ idUser
                $idUtilisateur = $idFestival = 1; //STUB
                sendJson(FavorisService::ajouterFavoris($idFestival, $idUtilisateur, getPDO()));
            }
        } catch (PDOException $e) {
            sendJson(getErrorArray('Internal server error', 500, $e), 500);
        }
        break;
    default:
        sendJson(getErrorArray('Not found', 404, 'Request not found'), 404);
        break;
}       