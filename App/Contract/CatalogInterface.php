<?php

namespace App\Contract;

/**
 * Catalog-specific repository contract.
 *
 * CRUD methods are inherited from BaseInterface.
 */
interface CatalogInterface extends BaseInterface
{
    // Get random catalog items
    public function getRandomCatalog();
}
