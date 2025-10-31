<?php

declare(strict_types=1);

namespace App\Model;

use Exception;

use Pecee\Pixie\QueryBuilder\QueryBuilderHandler;

final class MasterDataModel extends BaseModel
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
}
