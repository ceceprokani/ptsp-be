<?php

declare(strict_types=1);

namespace App\Model;

/**
 * HelloModel class
 * Auther : Cecep Rokani
 */
class BaseModel
{
    protected $database;

    protected function db() {
        $pdo = new \Pecee\Pixie\QueryBuilder\QueryBuilderHandler($this->database);
        return $pdo;
    }
    
    public function __construct(\Pecee\Pixie\Connection $database) {
        $this->database       = $database;
    }

    public function insert($table, $data) {
        return $this->db()->table($table)->insert($data);
    }

    public function update($id, $table, $data) {
        $checkData  = $this->fetchBy($id, $table);
        $result     = 0;
        
        if(!empty($checkData->id)) {
            $result = $this->db()->table($table)->where('id', $id)->update($data);
        }

        return $result;
    }

    public function delete($id, $table, $mode='SOFT') {
        if ($mode == 'SOFT') {
            $data = [
                'deleted_at' => date('Y-m-d H:i:s')
            ];
            return $this->db()->table($table)->where('id', $id)->update($data);
        } else {
            return $this->db()->table($table)->where('id', $id)->delete();
        }
    }
    
    public function deleteWhere($where, $table, $mode='SOFT') {
        $processDelete = $this->db()->table($table);
        foreach (array_filter($where) as $row) {
            if ($row['type'] == 'NOT_IN') {
                $processDelete->whereNotIn($row['field'], $row['value']);
            } elseif ($row['type'] == 'IN') {
                $processDelete->whereIn($row['field'], $row['value']);
            } elseif ($row['type'] === NULL) {
                $processDelete->whereNull($row['field']);
            } else {
                $processDelete->where($row['field'], $row['value']);
            }
        }

        if ($mode == 'SOFT') {
            $data = [
                'deleted_at' => date('Y-m-d H:i:s')
            ];
            return $processDelete->update($data);
        } else {
            return $processDelete->delete();
        }
    }
    
    public function fetchBy($id, $table, $checkDeleted=true) {
        $result = $this->db()->table($table)->where('id', $id);
        if ($checkDeleted) {
            $result->whereNull('deleted_at');
        }
        return $result->first();
    }

    public function fetch($table, $checkDeleted=true, $listFields=[]) {
        $result = $this->db()->table($table);

        if (!empty($listFields)) {
            $result->select($listFields);
        } if ($checkDeleted) {
            $result->whereNull('deleted_at');
        }

        return $result->get();
    }

    public function fetchWhere($where, $table, $mode="FIRST", $checkDeleted=true, $orderBy=[]) {
        $query = $this->db()->table($table);

        foreach ($where as $row) {
            if (!empty($row['value'])) {
                if (isset($row['type'])) {
                    if ($row['type'] == 'IN') {
                        $query->whereIn($row['field'], $row['value']);
                    } elseif ($row['type'] == 'NOT_IN') {
                        $query->whereNotIn($row['field'], $row['value']);
                    }
                } else {
                    $query->where($row['field'], $row['value']);
                }
            }
        }

        if (!empty($orderBy)) {
            $query->orderBy($orderBy[0], $orderBy[1]);
        } if ($checkDeleted) {
            $query->whereNull('deleted_at');
        }

        if ($mode == 'FIRST') {
            return $query->first();
        } elseif ($mode == 'COUNT') {
            return $query->count();
        } else {
            return $query->get();
        }
    }

    public function count($table) {
        $query = $this->db()->table($table)->where('code', $code);
        return $query->count();
    }

    public function fetchSingleData($table, $where, $type = 'all')
    {
        $query = $this->db()->table($table);
        if (!empty($where)) {
            foreach ($where as $field=>$value) {
                $query->where($field, $value);
            }
        }

        if($type == 'all') {
            return $query->get();
        } else {
            return $query->first();
        }
    }
}