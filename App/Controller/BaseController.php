<?php

namespace App\Controller;

class BaseController
{
    protected function render404(
        string $message = ''
    ): void {
        http_response_code(404);

        $pageTitle = 'Not Found';
        $errorMessage = $message;
        $section = '';
        $hideSearch = true;

        require BASE_PATH . '/view/errors/404.php';
        exit;
    }

    protected function render500(
        string $message = ''
    ): void {
        http_response_code(500);

        $pageTitle = 'Server Error';
        $errorMessage = $message;
        $section = '';
        $hideSearch = true;

        require BASE_PATH . '/view/errors/500.php';
        exit;
    }
}
