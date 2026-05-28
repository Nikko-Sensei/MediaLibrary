<?php

namespace App\Repository;

use App\Contract\CatalogInterface;
use App\Model\Catalog;

use PDO;

/**
 * Handles catalog database operations using PDO
 * and communicates with stored procedures.
 */
class CatalogRepository extends BaseRepository implements CatalogInterface
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'view_catalog', 'media_id');
    }


    public function getRandomCatalog()
    {
        $result = $this->db->query('SELECT * FROM view_random');

        return $result->fetchAll(PDO::FETCH_ASSOC);
    }

    public function read(int $id)
    {
        $statement = $this->db->prepare(
            'SELECT * FROM view_item_detail WHERE media_id = ?'
        );
        $statement->execute([$id]);

        $rows = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (empty($rows)) {
            return null;
        }

        $item = $rows[0];

        foreach ($rows as $row) {
            if (empty($row['role']) || empty($row['fullname'])) {
                continue;
            }

            $role = strtolower($row['role']);
            $item[$role][] = $row['fullname'];
        }

        return $item;
    }

    protected function mapToModel(array $row): object
    {
        return new Catalog(
            $row['media_id'],
            $row['title'] ?? null,
            $row['description'] ?? null,
            $row
        );
    }
}
