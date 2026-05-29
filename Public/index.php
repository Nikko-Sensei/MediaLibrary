<?php

use App\Core\GlobalExceptionHandler;
use App\Core\Router;
use App\DB\Database;
use App\Repository\CatalogRepository;
use App\Repository\FormatRepository;
use App\Repository\UserRepository;
use App\Service\CatalogService;
use App\Service\FormatService;
use App\Service\UserService;

/**
 * Main application entry point.
 * Initializes dependencies, services, and application routing.
 */

/*
 * //Report simple running errors
 * error_reporting(E_ALL);
 * //Make sure they are on screen
 * ini_set('display_errors',1);
 * //HTML formatted errors
 * ini_set('html_errors',1);
 *         OR
 * use @ in front of error
 */
define('BASE_PATH', dirname(__DIR__));

if (!defined('BASE_URL')) {
    define('BASE_URL', '/MediaLibrary-MVC--master');
}

require_once BASE_PATH . '/vendor/autoload.php';

GlobalExceptionHandler::register();

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/* BUILD SHARED OBJECTS */

$db = Database::getConnection();

/* Repositories */
$catalogRepo = new CatalogRepository($db);
$formatRepo = new FormatRepository($db);

/* Services */
$catalogService = new CatalogService($catalogRepo);
$formatService = new FormatService($formatRepo);
$userRepo = new UserRepository($db);
$userService = new UserService($userRepo);


/*
 * Request flow:
 * index.php -> Core\Router -> controller -> service -> repository -> PDO/database
 *
 * Exception flow:
 * any layer throws -> GlobalExceptionHandler -> logs/error.log -> friendly response
 */
$router = new Router($catalogService, $formatService, $userService);
$router->dispatch($_GET['page'] ?? 'home');
