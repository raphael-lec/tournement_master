<?php
// Fic<?php
// Fichier : index.php
define('ROOT', dirname(__FILE__) . DIRECTORY_SEPARATOR);

// 1. Chargement de l'autoloader
require_once ROOT . 'vendor/autoload.php';

// 2. Démarrage de la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 3. Variables d'environnement
use Dotenv\Dotenv;
$dotenv = Dotenv::createImmutable(ROOT);
$dotenv->load(); 

// 4. Détecteur et chargement du Routeur
if (file_exists(ROOT . 'Router.php')) {
    require_once ROOT . 'Router.php';
} elseif (file_exists(ROOT . 'Services/Router.php')) {
    require_once ROOT . 'Services/Router.php';
} elseif (file_exists(ROOT . 'Service/Router.php')) {
    require_once ROOT . 'Service/Router.php';
} elseif (file_exists(ROOT . 'config/Router.php')) {
    require_once ROOT . 'config/Router.php';
} else {
    // Si PHP ne le trouve nulle part, il va nous afficher le bon dossier !
    die("Désolé, je ne trouve pas 'Router.php'. Vérifie dans ton explorateur de fichiers (à gauche dans VS Code) : dans quel dossier est-il rangé ?");
}

$router = new Router(); 
$router->handleRequest($_GET);