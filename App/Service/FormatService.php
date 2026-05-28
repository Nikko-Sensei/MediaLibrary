<?php

namespace App\Service;

use App\Contract\FormatInterface;
use App\DB\Database;
use App\Repository\FormatRepository;

/**
 * Handles format-related business logic and manages
 * communication between controllers and repositories.
 */
class FormatService
{
    private FormatInterface $repo;

    public function __construct(?FormatInterface $repo = null)
    {
        // Create default repository if none is provided
        if ($repo === null) {
            $db = Database::getConnection();
            $repo = new FormatRepository($db);
        }

        $this->repo = $repo;
    }

    // Get format dropdown data
    public function formatArray($category = null)
    {
        return $this->repo->getFormatDropDown($category);
    }

    // Get category dropdown data
    public function categoryDropDown()
    {
        return $this->repo->getCategoryDropDown();
    }

    // Get genres dropdown data
    public function genresArray($category = null)
    {
        return $this->repo->getGenresDropDown($category);
    }
}
