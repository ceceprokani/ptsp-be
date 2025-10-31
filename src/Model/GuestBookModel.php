<?php

declare(strict_types=1);

namespace App\Model;

use Exception;

use Pecee\Pixie\QueryBuilder\QueryBuilderHandler;

final class GuestBookModel extends BaseModel
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
        $getQuery = $this->db()->table('module_guestbook_guest');
        $getQuery->select($getQuery->raw('module_guestbook_guest.*, module_master_data_organization.name as organization_name, module_master_data_purpose.name as purpose_name'));
        $getQuery->leftJoin('module_master_data_organization', 'module_guestbook_guest.organization_id', '=', 'module_master_data_organization.id');
        $getQuery->leftJoin('module_master_data_purpose', 'module_guestbook_guest.purpose_id', '=', 'module_master_data_purpose.id');

        if (!empty($params['start']) && !empty($params['end'])) {
            $getQuery->where(function(QueryBuilderHandler $queryBuilder) use ($params) {
                $queryBuilder->where($queryBuilder->raw("date(module_guestbook_guest.created_at)"), '>=', $params['start']);
                $queryBuilder->where($queryBuilder->raw("date(module_guestbook_guest.created_at)"), '<=', $params['end']);
            });
        } if (!empty($params['keywords'])) {
            $keywords = $params['keywords'];
            $getQuery->where(function(QueryBuilderHandler $queryBuilder) use ($keywords) {
                $queryBuilder->where("module_guestbook_guest.name", 'LIKE', "%$keywords%");
                $queryBuilder->orWhere("module_master_data_organization.name", 'LIKE', "%$keywords%");
                $queryBuilder->orWhere("module_guestbook_guest.phone", 'LIKE', "%$keywords%");
            });
        }

        $getQuery->whereNull('module_guestbook_guest.deleted_at');

        $getQuery->orderBy('module_guestbook_guest.id', 'asc');

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
        
        return ['data' => $list, 'total' => $totalData];
    }

    public function save($params) {
        $result                 = ['status' => false, 'message' => 'Data gagal disimpan'];
        
        $data = $params;
        if (empty($params['id'])) {
            $data = array_merge(['created_at' => date('Y-m-d H:i:s')], $data);
            $userId = $this->db()->table('module_guestbook_guest')->insert($data);
        } else {
            $userId = $params['id'];
            $this->db()->table('module_guestbook_guest')->where('id', $params['id'])->update($data);
        }
        $result                 = ['status' => true, 'message' => 'Data gagal disimpan'];

        return $result;
    }

    public function detail($id) {
        $result = $this->db()->table('module_guestbook_guest')->where('id', $id)->first();

        return $result;
    }

    public function deleteData($id) {
        $result                 = ['status' => false, 'message' => 'Data gagal dihapus'];

        $checkData = $this->db()->table('module_guestbook_guest')->where('id', $id)->first();

        if (!empty($checkData)) {
            $process = $this->delete($id, 'module_guestbook_guest');
            
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

    public function buildQueryListEvent($params=null) {
        $getQuery = $this->db()->table('module_guestbook_event');
        $getQuery->select($getQuery->raw('module_guestbook_event.*, user_staff.name as pic_name'));
        $getQuery->leftJoin('user_staff', 'module_guestbook_event.pic_id', '=', 'user_staff.id');

        if (!empty($params['start']) && !empty($params['end'])) {
            $getQuery->where(function(QueryBuilderHandler $queryBuilder) use ($params) {
                $queryBuilder->where("module_guestbook_event.start_date", '>=', $params['start']);
                $queryBuilder->where("module_guestbook_event.end_date", '<=', $params['end']);
            });
        } if (!empty($params['keywords'])) {
            $keywords = $params['keywords'];
            $getQuery->where(function(QueryBuilderHandler $queryBuilder) use ($keywords) {
                $queryBuilder->where("module_guestbook_event.name", 'LIKE', "%$keywords%");
            });
        }

        $getQuery->whereNull('module_guestbook_event.deleted_at');
        $getQuery->orderBy('module_guestbook_event.id', 'asc');

        return $getQuery;
    }

    public function listEvent($params)
    {
        $getQuery = $this->buildQueryListEvent($params);

        $totalData = $getQuery->count();

        if (!empty($params['page'])) {
            $page = $params['page'] == 1 ? $params['page'] - 1 : ($params['page'] * $params['limit']) - $params['limit'];

            $getQuery->limit((int) $params['limit']);
            $getQuery->offset((int) $page);
        }

        $list = $getQuery->get();
        
        return ['data' => $list, 'total' => $totalData];
    }

    public function saveEvent($params) {
        $result                 = ['status' => false, 'message' => 'Data gagal disimpan'];
        
        $data = $params;
        if (empty($params['id'])) {
            $data = array_merge(['created_at' => date('Y-m-d H:i:s')], $data);
            $userId = $this->db()->table('module_guestbook_event')->insert($data);
        } else {
            $userId = $params['id'];
            $this->db()->table('module_guestbook_event')->where('id', $params['id'])->update($data);
        }
        $result                 = ['status' => true, 'message' => 'Data gagal disimpan'];

        return $result;
    }

    public function registerEvent($params) {
        $result                 = ['status' => false, 'message' => 'Data gagal disimpan'];
        
        $data = [
            'id' => $params['id'] ?? null,
            'name' => $params['name'],
            'email' => $params['email'],
            'phone' => $params['phone'],
            'organization_id' => $params['organization_id'],
            'address' => $params['address'],
        ];
        if (empty($params['id'])) {
            $data = array_merge(['created_at' => date('Y-m-d H:i:s')], $data);
            $userId = $this->db()->table('module_guestbook_guest')->insert($data);
        } else {
            $userId = $params['id'];
            $this->db()->table('module_guestbook_guest')->where('id', $params['id'])->update($data);
        }

        if ($userId) {
            $dataEvent = [
                'guest_id' => $userId,
                'event_id' => $params['event_id'],
                'created_at' => date('Y-m-d H:i:s')
            ];
            $this->db()->table('module_guestbook_event_registration')->insert($dataEvent);
        }
        $result                 = ['status' => true, 'message' => 'Data berhasil disimpan'];

        return $result;
    }

    public function detailEvent($id) {
        $result = $this->db()->table('module_guestbook_event')->where('id', $id)->first();

        return $result;
    }

    public function deleteDataEvent($id) {
        $result                 = ['status' => false, 'message' => 'Data gagal dihapus'];

        $checkData = $this->db()->table('module_guestbook_event')->where('id', $id)->first();

        if (!empty($checkData)) {
            $process = $this->delete($id, 'module_guestbook_event');

            if ($process) {
                $result                 = ['status' => true, 'message' => 'Data berhasil dihapus'];
            }
        }

        return $result;
    }

    public function deleteEventGuest($id) {
        $result                 = ['status' => false, 'message' => 'Data gagal dihapus'];

        $checkData = $this->db()->table('module_guestbook_guest')->where('id', $id)->first();

        if (!empty($checkData)) {
            $process = $this->delete($id, 'module_guestbook_guest');

            if ($process) {
                $result                 = ['status' => true, 'message' => 'Data berhasil dihapus'];
            }
        }

        return $result;
    }

    public function deleteBatch($listId) {
        $result                 = ['status' => false, 'message' => 'Data gagal dihapus'];

        $checkData = $this->db()->table('module_guestbook_guest')->whereIn('id', $listId)->get();

        if (!empty($checkData)) {
            $totalSuccess = 0;
            foreach ($checkData as $data) {
                $process = $this->db()->table('module_guestbook_guest')->where('id', $id)->delete();
                $totalSuccess += $process ? 1 : 0;
            }

            if ($totalSuccess) {
                $result                 = ['status' => true, 'message' => 'Data berhasil dihapus'];
            }
        }

        return $result;
    }

    public function buildQueryListEventMember($params=null) {
        $getQuery = $this->db()->table('module_guestbook_event');
        $getQuery->select($getQuery->raw('module_guestbook_event_registration.created_at as registration_date, module_guestbook_guest.id, module_guestbook_guest.name, module_master_data_organization.name as organization_name, module_guestbook_guest.email, module_guestbook_guest.phone, module_guestbook_guest.name as organzation'));
        $getQuery->innerJoin('module_guestbook_event_registration', 'module_guestbook_event_registration.event_id', '=', 'module_guestbook_event.id');
        $getQuery->innerJoin('module_guestbook_guest', 'module_guestbook_event_registration.guest_id', '=', 'module_guestbook_guest.id');
        $getQuery->leftJoin('module_master_data_organization', 'module_guestbook_guest.organization_id', '=', 'module_master_data_organization.id');

        if (!empty($params['event_id'])) {
            $getQuery->where('module_guestbook_event_registration.event_id', $params['event_id']);
        } if (!empty($params['keywords'])) {
            $keywords = $params['keywords'];
            $getQuery->where(function(QueryBuilderHandler $queryBuilder) use ($keywords) {
                $queryBuilder->where("module_guestbook_guest.name", 'LIKE', "%$keywords%");
                $queryBuilder->orWhere("module_master_data_organization.name", 'LIKE', "%$keywords%");
            });
        }

        $getQuery->whereNull('module_guestbook_guest.deleted_at');
        $getQuery->whereNull('module_guestbook_event.deleted_at');
        $getQuery->orderBy('module_guestbook_event.id', 'asc');

        return $getQuery;
    }

    public function listEventMember($params)
    {
        $getQuery = $this->buildQueryListEventMember($params);

        $totalData = $getQuery->count();

        if (!empty($params['page'])) {
            $page = $params['page'] == 1 ? $params['page'] - 1 : ($params['page'] * $params['limit']) - $params['limit'];

            $getQuery->limit((int) $params['limit']);
            $getQuery->offset((int) $page);
        }

        $list = $getQuery->get();
        
        return ['data' => $list, 'total' => $totalData];
    }
}
