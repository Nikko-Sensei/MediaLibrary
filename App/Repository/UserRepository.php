<?php

namespace App\Repository;

use App\Contract\UserInterface;
use App\Model\User;
use App\Repository\BaseRepository;
use PDO;

class UserRepository extends BaseRepository implements UserInterface
{
    public function __construct(PDO $db)
    {
        parent::__construct($db, 'users', 'user_id');
    }

    public function findByUsername(string $username)
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE username = :username');
        $stmt->execute(['username' => $username]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->mapToModel($row) : null;
    }

    public function findByEmail(string $email)
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = :email');
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ? $this->mapToModel($row) : null;
    }

    protected function mapToModel(array $row): object
    {
        return User::fromArray($row);
    }
}
