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
use App\Request\LoginRequest;
use App\Service\CatalogService;
use App\Service\FormatService;
use App\Service\UserService;
use App\Validate\Validator;
use App\Request\RegisterUserRequest;




$page = $_GET['page'] ?? 'home';

switch ($page) {
    case 'details':
        $controller = new DetailsController($catalogService);
        $controller->show();
        break;

    case 'suggest':
        $controller = new SuggestController($formatService);
        $controller->index();
        break;

    case 'catalog':
        $controller = new CatalogController($catalogService);
        $controller->index();
        break;

    case 'login':
        require_once BASE_PATH . '/App/Controller/AuthController.php';
        $controller = new AuthController($userService);
        $controller->login(new LoginRequest(), new Validator());
        break;

    case 'register':
        require_once BASE_PATH . '/App/Controller/AuthController.php';
        $controller = new AuthController($userService);
        $controller->register(new RegisterUserRequest(), new Validator());
        break;

    case 'logout':
        require_once BASE_PATH . '/App/Controller/AuthController.php';
        $controller = new AuthController($userService);
        $controller->logout();
        break;

    default:  // HOME PAGE
        $controller = new CatalogController($catalogService);
        $controller->home();
}