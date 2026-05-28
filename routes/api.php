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



$page = $_GET['page'] ?? 'home';

switch ($page) {

    case 'api/catalog':
        require_once BASE_PATH . '/App/Controller/api/CatalogApiController.php';
        $controller = new CatalogApiController($catalogService);
        $controller->index();
        break;

    case 'api/details':
        require_once BASE_PATH . '/App/Controller/api/DetailsApiController.php';
        $controller = new DetailsApiController($catalogService);
        $controller->show();
        break;

    case 'api/suggest':
        require_once BASE_PATH . '/App/Controller/api/SuggestApiController.php';
        $controller = new SuggestApiController($formatService);
        $controller->index();
        break;

    default:  // HOME PAGE
        $controller = new CatalogController($catalogService);
        $controller->home();
}
