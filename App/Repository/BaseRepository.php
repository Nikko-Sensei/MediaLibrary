<?php

namespace App\Repository;

use App\Contract\BaseInterface;
use BadMethodCallException;
use PDO;

abstract class BaseRepository implements BaseInterface
{
    protected PDO $db;
    protected string $table;
    protected string $primaryKey;

    public function __construct(PDO $db, string $table, string $primaryKey = 'id')
    {
        $this->db = $db;
        $this->table = $table;
        $this->primaryKey = $primaryKey;
    }

    abstract protected function mapToModel(array $row): object;
    
    protected function quoteIdentifier(string $identifier): string
    {
        return '`' . str_replace('`', '``', $identifier) . '`';
    }

    protected function assertTableIsConfigured(): void
    {
        if (trim($this->table) === '') {
            throw new BadMethodCallException('Repository table name is not configured.');
        }
    }

    public function create(array $data)
    {
        $this->assertTableIsConfigured();

        if (empty($data)) {
            throw new BadMethodCallException('Create requires non-empty data.');
        }

        $columns = array_keys($data);
        $columnList = implode(', ', array_map([$this, 'quoteIdentifier'], $columns));
        $placeholders = implode(', ', array_fill(0, count($columns), '?'));

        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            $this->quoteIdentifier($this->table),
            $columnList,
            $placeholders
        );

        $statement = $this->db->prepare($sql);
        $statement->execute(array_values($data));

        return $this->db->lastInsertId();
    }

    public function read(int $id)
    {
        $this->assertTableIsConfigured();

        $sql = sprintf(
            'SELECT * FROM %s WHERE %s = ? LIMIT 1',
            $this->quoteIdentifier($this->table),
            $this->quoteIdentifier($this->primaryKey)
        );

        $statement = $this->db->prepare($sql);
        $statement->execute([$id]);

        $row = $statement->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->mapToModel($row) : null;
        // $row = $statement->fetch(PDO::FETCH_ASSOC);

        // return $row === false ? null : $row;
    }

    public function update(int $id, array $data)
    {
        $this->assertTableIsConfigured();

        if (empty($data)) {
            throw new BadMethodCallException('Update requires non-empty data.');
        }

        $columns = array_keys($data);
        $setClauses = implode(', ', array_map(function ($column) {
            return sprintf('%s = ?', $this->quoteIdentifier($column));
        }, $columns));

        $sql = sprintf(
            'UPDATE %s SET %s WHERE %s = ?',
            $this->quoteIdentifier($this->table),
            $setClauses,
            $this->quoteIdentifier($this->primaryKey)
        );

        $statement = $this->db->prepare($sql);
        $values = array_values($data);
        $values[] = $id;

        return $statement->execute($values);
    }

    public function getAll(array $criteria = [], $limit = null, $offset = null)
    {
        $this->assertTableIsConfigured();

        $params = [];
        $where = $this->buildWhereClause($criteria, $params);

        $sql = sprintf('SELECT * FROM %s', $this->quoteIdentifier($this->table));

        if ($where !== '') {
            $sql .= ' WHERE ' . $where;
        }

        if ($limit !== null) {
            $sql .= ' LIMIT ' . (int) $limit;

            if ($offset !== null) {
                $sql .= ' OFFSET ' . (int) $offset;
            }
        }

        $statement = $this->db->prepare($sql);
        $statement->execute($params);


        return array_map(
            fn($row) => $this->mapToModel($row),
            $statement->fetchAll(PDO::FETCH_ASSOC)
        );
        // return array_map(
        //     [$this, 'formatRow'],
        //     $statement->fetchAll(PDO::FETCH_ASSOC)
        // );
    }

    // protected function formatRow(array $row): array
    // {
    //     return $row;
    // }




    public function count(array $criteria = [])
    {
        $this->assertTableIsConfigured();

        $params = [];
        $where = $this->buildWhereClause($criteria, $params);

        $sql = sprintf('SELECT COUNT(*) FROM %s', $this->quoteIdentifier($this->table));

        if ($where !== '') {
            $sql .= ' WHERE ' . $where;
        }

        $statement = $this->db->prepare($sql);
        $statement->execute($params);

        return (int) $statement->fetchColumn();
    }

    protected function buildWhereClause(array $criteria, array &$params = []): string
    {
        $clauses = [];

        foreach ($criteria as $column => $value) {
            $columnName = $this->quoteIdentifier($column);

            if ($value === null) {
                $clauses[] = sprintf('%s IS NULL', $columnName);
                continue;
            }

            if (is_string($value) && strpos($value, '%') !== false) {
                $clauses[] = sprintf('%s LIKE ?', $columnName);
                $params[] = $value;
                continue;
            }

            $clauses[] = sprintf('%s = ?', $columnName);
            $params[] = $value;
        }

        return implode(' AND ', $clauses);
    }

    public function delete(int $id)
    {
        $this->assertTableIsConfigured();

        $sql = sprintf(
            'DELETE FROM %s WHERE %s = ?',
            $this->quoteIdentifier($this->table),
            $this->quoteIdentifier($this->primaryKey)
        );

        $statement = $this->db->prepare($sql);

        return $statement->execute([$id]);
    }
}