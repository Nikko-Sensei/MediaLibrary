<?php

use App\Controller\api\CatalogApiController;
use App\Controller\api\DetailsApiController;
use App\Controller\api\SuggestApiController;
use App\Controller\AuthController;
use App\Controller\CatalogController;
use App\Controller\DetailsController;
use App\Controller\SuggestController;
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

require_once BASE_PATH . '/vendor/autoload.php';

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


$page = $_GET['page'] ?? 'home';
if (strpos($page, 'api/') === 0) {
    require_once BASE_PATH . '/routes/api.php';
} else {
    require_once BASE_PATH . '/routes/web.php';
}
