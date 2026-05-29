<?php

namespace App\Repository;

use App\Contract\UserInterface;
use App\Model\User;
use PDO;

class UserRepository extends BaseRepository implements UserInterface
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'users', 'user_id');
    }

    protected function mapToModel(array $row): User
    {
        return new User(
            $row['user_id'],
            $row['username'],
            $row['email'],
            $row['password']
        );
    }

    public function findByUsername(string $username): ?User
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM users WHERE username = :username LIMIT 1'
        );

        $stmt->execute([
            'username' => $username
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row
            ? $this->mapToModel($row)
            : null;
    }

    public function findByEmail(string $email): ?User
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM users WHERE email = :email LIMIT 1'
        );

        $stmt->execute([
            'email' => $email
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row
            ? $this->mapToModel($row)
            : null;
    }
}