<?php

declare(strict_types=1);

namespace App\Controller\Staff;

use App\Helper\JsonResponse;
use Pimple\Psr11\Container;

use App\Model\AuthModel;
use App\Model\GuestBookModel;
use App\Helper\FileUpload;
use App\Helper\General;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class GuestBook
{
    private $container;
    private $auth;
    private $user;
    private $model;
    private $general;
    private $file;
    
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->auth                 = new AuthModel($this->container->get('db'));
        $this->model                = new GuestBookModel($this->container->get('db'));
        $this->file                 = new FileUpload($this->container);
        $this->general              = new General($this->container);
        $this->user                 = $this->auth->validateToken();

        $roles                      = array('superadmin', 'admin');

        if(!in_array($this->user->role, $roles)) {
            $this->auth->denyAccess();
        }
    }

    public function index(Request $request, Response $response): Response
    {
        $params = $request->getQueryParams();
        $result = ['status' => false, 'message' => 'Data tidak ditemukan', 'data' => array()];
        $list   = $this->model->list($params);

        if (!empty($list['data'])) {
            $result = ['status' => true, 'message' => 'Data ditemukan', 'data' => $list['data']];
        }

        if (!empty($params['page'])) {
            $result['pagination'] = [
                'page' => (int) $params['page'],
                'prev' => $params['page'] > 1,
                'next' => ($list['total'] - ($params['page'] * $params['limit'])) > 0,
                'total' => $list['total']
            ];
        }
        
        return JsonResponse::withJson($response, $result, 200);
    }

    public function detail(Request $request, Response $response): Response
    {
        $params = $request->getQueryParams();

        $result = ['status' => false, 'message' => 'Data tidak ditemukan', 'data' => array()];
        
        $data   = $this->model->detail($params['id']);
        if (!empty($data)) {
            $result = ['status' => true, 'message' => 'Data berhasil ditemukan', 'data' => $data];
        }

        return JsonResponse::withJson($response, $result, 200);
    }

    public function save(Request $request, Response $response): Response
    {
        $post                   = $request->getParsedBody();
        $data                   = [
            'id' => $post['id'] ?? null,
            'name' => $post['name'],
            'email' => $post['email'],
            'phone' => $post['phone'],
            'organization_id' => $post['organization_id'],
            'purpose_id' => $post['purpose_id'],
            'address' => $post['address'],
            'date' => $post['date'],
        ];

        if (isset($_FILES['attachment']) && $_FILES['attachment']['size'] != 0) {
            // uploading photo 
            $targetFolder   = "guestbook/guest";
            $validateFile   = $this->file->validateFile('attachment', $targetFolder, true);

            if ($validateFile['status']) {
                $allowed_extension = array('pdf','doc');
                if (in_array($validateFile['extension'], $allowed_extension)) {
                    $uploadedFiles  = $request->getUploadedFiles();
                    $uploadedFile   = $uploadedFiles['attachment'];

                    if ($uploadedFile->getError() === UPLOAD_ERR_OK) {
                        $filename      = $this->file->moveUploadedFile($uploadedFile, 'attachment', $targetFolder);
                        $url_photo     = $this->general->baseUrl($filename);
                        $data['attachment'] = $url_photo;
                    }

                } else {
                    $allowSubmit        = false;
                    $result['status']   = false;
                    $result['message']  = "File yang diupload harus Gambar";
                }
            }
        }

        $result                 = $this->model->save($data);

        return JsonResponse::withJson($response, $result, 200);
    }

    public function delete(Request $request, Response $response, $parameters): Response
    {
        $result = $this->model->deleteData($parameters['id']);
        return JsonResponse::withJson($response, $result, 200);
    }

    public function listEvent(Request $request, Response $response): Response
    {
        $params = $request->getQueryParams();
        $result = ['status' => false, 'message' => 'Data tidak ditemukan', 'data' => array()];
        $list   = $this->model->listEvent($params);

        if (!empty($list['data'])) {
            $result = ['status' => true, 'message' => 'Data ditemukan', 'data' => $list['data']];
        }

        if (!empty($params['page'])) {
            $result['pagination'] = [
                'page' => (int) $params['page'],
                'prev' => $params['page'] > 1,
                'next' => ($list['total'] - ($params['page'] * $params['limit'])) > 0,
                'total' => $list['total']
            ];
        }
        
        return JsonResponse::withJson($response, $result, 200);
    }

    public function listEventMember(Request $request, Response $response): Response
    {
        $params = $request->getQueryParams();
        $result = ['status' => false, 'message' => 'Data tidak ditemukan', 'data' => array()];
        $list   = $this->model->listEventMember($params);

        if (!empty($list['data'])) {
            $result = ['status' => true, 'message' => 'Data ditemukan', 'data' => $list['data']];
        }

        if (!empty($params['page'])) {
            $result['pagination'] = [
                'page' => (int) $params['page'],
                'prev' => $params['page'] > 1,
                'next' => ($list['total'] - ($params['page'] * $params['limit'])) > 0,
                'total' => $list['total']
            ];
        }
        
        return JsonResponse::withJson($response, $result, 200);
    }

    public function detailEvent(Request $request, Response $response): Response
    {
        $params = $request->getQueryParams();

        $result = ['status' => false, 'message' => 'Data tidak ditemukan', 'data' => array()];
        
        $data   = $this->model->detailEvent($params['id']);
        if (!empty($data)) {
            $result = ['status' => true, 'message' => 'Data berhasil ditemukan', 'data' => $data];
        }

        return JsonResponse::withJson($response, $result, 200);
    }

    public function saveEvent(Request $request, Response $response): Response
    {
        $post                   = $request->getParsedBody();
        $data                   = [
            'id' => !empty($post['id']) ? $post['id'] : null,
            'name' => $post['name'],
            'start_date' => $post['start_date'],
            'end_date' => $post['end_date'],
            'capacity' => $post['capacity'],
            'pic_id' => $post['pic_id'],
            'description' => $post['description'],
            'user_id' => $this->user->id,
        ];

        $result                 = $this->model->saveEvent($data);

        return JsonResponse::withJson($response, $result, 200);
    }

    public function deleteEvent(Request $request, Response $response, $parameters): Response
    {
        $result = $this->model->deleteDataEvent($parameters['id']);
        return JsonResponse::withJson($response, $result, 200);
    }

    public function deleteEventGuest(Request $request, Response $response, $parameters): Response
    {
        $result = $this->model->deleteEventGuest($parameters['id']);
        return JsonResponse::withJson($response, $result, 200);
    }
}
