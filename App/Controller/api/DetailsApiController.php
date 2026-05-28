<?php
namespace App\Controller\api;

use App\Service\CatalogService;

class DetailsApiController
{
    private CatalogService $catalogService;

    public function __construct(CatalogService $catalogService)
    {
        // Inject catalog service dependency
        $this->catalogService = $catalogService;
    }

    // Show item details API
    public function show(): void
    {
        // Return JSON response
        header('Content-Type: application/json');

        // Validate item ID from URL
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

        // Invalid ID response
        if (!$id) {
            http_response_code(400);

            echo json_encode([
                'success' => false,
                'message' => 'Invalid item ID'
            ]);

            return;
        }

        // Get item data from service
        $item = $this->catalogService->singleItemArray($id);

        // Item not found response
        if (empty($item)) {
            http_response_code(404);

            echo json_encode([
                'success' => false,
                'message' => 'Item not found'
            ]);

            return;
        }

        // Success response
        http_response_code(200);

        echo json_encode([
            'success' => true,
            'data' => [
                'pageTitle' => $item['title'],
                'section' => $item['category'],
                'item' => $item
            ]
        ]);
    }
}
