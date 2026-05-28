<?php
namespace App\Contract;

use App\Contract\BaseInterface;

interface UserInterface extends BaseInterface
{
    public function findByUsername(string $username);
    public function findByEmail(string $email);
}
