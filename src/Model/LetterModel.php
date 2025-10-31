<?php

declare(strict_types=1);

namespace App\Model;

use Exception;

use Pecee\Pixie\QueryBuilder\QueryBuilderHandler;

final class LetterModel extends BaseModel
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

    public function buildQueryList($params=null) {
        $getQuery = $this->db()->table('module_letter_incoming');

        if (!empty($params['keywords'])) {
            $keywords = $params['keywords'];
            $getQuery->where(function(QueryBuilderHandler $queryBuilder) use ($keywords) {
                $queryBuilder->where("number", 'LIKE', "%$keywords%");
                $queryBuilder->orWhere("sender", 'LIKE', "%$keywords%");
                $queryBuilder->orWhere("regarding", 'LIKE', "%$keywords%");
            });
        }

        $getQuery->whereNull('deleted_at');

        $getQuery->orderBy('module_letter_incoming.id', 'asc');

        return $getQuery;
    }

    public function list($params)
    {
        $getQuery = $this->buildQueryList($params);
        
        $totalData = $getQuery->count();

        if (!empty($params['page'])) {
            $page = $params['page'] == 1 ? $params['page'] - 1 : ($params['page'] * $params['limit']) - $params['limit'];

            $getQuery->limit((int) $params['limit']);
            $getQuery->offset((int) $page);
        }

        $list = $getQuery->get();

        foreach ($list as $key => $value) {
            $listAssigned = $this->db()->table('module_letter_incoming_assigned')
                ->where('letter_incoming_id', $value->id)
                ->whereNull('deleted_at')
                ->get();

            $value->assigned = [
                'user' => !empty($listAssigned) ? array_column($listAssigned, 'user_id') : [],
                'description' => !empty($listAssigned) ? array_column($listAssigned, 'note')[0] : ''
            ];
        }
        
        return ['data' => $list, 'total' => $totalData];
    }

    public function save($params) {
        $result                 = ['status' => false, 'message' => 'Data gagal disimpan'];
        
        $data = $params;
        if (empty($params['id'])) {
            $data = array_merge(['created_at' => date('Y-m-d H:i:s')], $data);
            $userId = $this->db()->table('module_letter_incoming')->insert($data);
        } else {
            $userId = $params['id'];
            $this->db()->table('module_letter_incoming')->where('id', $params['id'])->update($data);
        }
        $result                 = ['status' => true, 'message' => 'Data gagal disimpan'];

        return $result;
    }

    public function detail($id) {
        $result = $this->db()->table('module_letter_incoming')->where('id', $id)->first();

        return $result;
    }

    public function deleteData($id) {
        $result                 = ['status' => false, 'message' => 'Data gagal dihapus'];

        $checkData = $this->db()->table('module_letter_incoming')->where('id', $id)->first();

        if (!empty($checkData)) {
            $process = $this->delete($id, 'module_letter_incoming');
            
            if ($process) {
                if (!empty($checkData->attachment)) {
                    $urlFile = $_SERVER['DOCUMENT_ROOT'] . parse_url($checkData->attachment, PHP_URL_PATH);
                    if (file_exists($urlFile)) {
                        unlink($urlFile);
                    }
                }

                $result                 = ['status' => true, 'message' => 'Data berhasil dihapus'];
            }
        }

        return $result;
    }

    public function deleteBatch($listId) {
        $result                 = ['status' => false, 'message' => 'Data gagal dihapus'];

        $checkData = $this->db()->table('module_letter_incoming')->whereIn('id', $listId)->get();

        if (!empty($checkData)) {
            $totalSuccess = 0;
            foreach ($checkData as $data) {
                $process = $this->db()->table('module_letter_incoming')->where('id', $id)->delete();
                $totalSuccess += $process ? 1 : 0;
            }

            if ($totalSuccess) {
                $result                 = ['status' => true, 'message' => 'Data berhasil dihapus'];
            }
        }

        return $result;
    }
}
