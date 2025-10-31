<?php

declare(strict_types=1);

namespace App\Model;

use Exception;

final class UserModel
{
    protected $database;

    protected function db()
    {
        $pdo = new \Pecee\Pixie\QueryBuilder\QueryBuilderHandler($this->database);
        return $pdo;
    }

    public function __construct(\Pecee\Pixie\Connection $database)
    {
        $this->database       = $database;
    }

    public function countUser()
    {
        return $this->db()->table('users')->count();
    }

    public function updatePassword($id, $password) {
        return $this->db()->table('users')->where('id', $id)->update(['password' => password_hash($password, PASSWORD_ARGON2ID)]);
    }
}
