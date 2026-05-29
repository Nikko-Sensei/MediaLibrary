<?php

namespace App\Core;

use App\Controller\api\CatalogApiController;
use App\Controller\api\DetailsApiController as ApiDetailsController;
use App\Controller\api\SuggestApiController as ApiSuggestController;
use App\Controller\AuthController;
use App\Controller\CatalogController;
use App\Controller\DetailsController;
use App\Controller\SuggestController;
use App\Exception\NotFoundException;
use App\Request\LoginRequest;
use App\Request\RegisterUserRequest;
use App\Service\CatalogService;
use App\Service\FormatService;
use App\Service\UserService;
use App\Validate\Validator;

class Router
{
    public function __construct(
        private CatalogService $catalogService,
        private FormatService $formatService,
        private UserService $userService
    ) {}

    public function dispatch(string $page): void
    {
        match ($page) {
            'home', 'index' => $this->home(),
            'details' => $this->details(),
            'suggest' => $this->suggest(),
            'catalog' => $this->catalog(),
            'login' => $this->login(),
            'register' => $this->register(),
            'logout' => $this->logout(),
            'api/catalog' => $this->apiCatalog(),
            'api/details' => $this->apiDetails(),
            'api/suggest' => $this->apiSuggest(),
            default => throw new NotFoundException('Page not found.')
        };
    }

    private function home(): void
    {
        (new CatalogController($this->catalogService))->home();
    }

    private function details(): void
    {
        (new DetailsController($this->catalogService))->show();
    }

    private function suggest(): void
    {
        (new SuggestController($this->formatService))->index();
    }

    private function catalog(): void
    {
        (new CatalogController($this->catalogService))->index();
    }

    private function login(): void
    {
        (new AuthController($this->userService))->login(
            new LoginRequest(),
            new Validator()
        );
    }

    private function register(): void
    {
        (new AuthController($this->userService))->register(
            new RegisterUserRequest(),
            new Validator()
        );
    }

    private function logout(): void
    {
        (new AuthController($this->userService))->logout();
    }

    private function apiCatalog(): void
    {
        (new CatalogApiController($this->catalogService))->index();
    }

    private function apiDetails(): void
    {
        (new ApiDetailsController($this->catalogService))->show();
    }

    private function apiSuggest(): void
    {
        (new ApiSuggestController($this->formatService))->index();
    }
}
