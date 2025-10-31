<?php

declare(strict_types=1);

namespace App\Controller\Staff;

use App\Helper\JsonResponse;
use Pimple\Psr11\Container;

use App\Model\AuthModel;
use App\Model\LetterModel;
use App\Helper\FileUpload;
use App\Helper\General;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Letter
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
        $this->model                = new LetterModel($this->container->get('db'));
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
            'id' => $post['id'],
            'number' => $post['number'],
            'sender' => $post['sender'],
            'regarding' => $post['regarding'],
            'description' => $post['description'],
            'date' => $post['date'],
            'user_id' => $this->user->id,
            'status' => $post['status'] ?? 'draft',
        ];

        if (isset($_FILES['attachment']) && $_FILES['attachment']['size'] != 0) {
            // uploading photo 
            $targetFolder   = "letter/incoming";
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

    public function assign(Request $request, Response $response): Response
    {
        $post                   = $request->getParsedBody();

        $listAssigned = $post['list_assigned'];

        // delete not assigned
        $this->model->deleteWhere(
            [
                [
                    'field' => 'letter_incoming_id',
                    'value' => $post['id_letter'],
                    'type'  => '='
                ],
                [
                    'field' => 'user_id',
                    'value' => $listAssigned,
                    'type'  => 'NOT_IN'
                ]
            ],
            'module_letter_incoming_assigned');

        $totalSuccess = 0;
        foreach ($listAssigned as $key => $value) {
            $checkExist = $this->model->fetchWhere(
                [
                    [
                        'field' => 'letter_incoming_id',
                        'value' => $post['id_letter']
                    ],
                    [
                        'field' => 'user_id',
                        'value' => $value
                    ]
                ],
                'module_letter_incoming_assigned');

            if (empty($checkExist)) {
                $data                   = [
                    'letter_incoming_id' => $post['id_letter'],
                    'note' => $post['description'],
                    'user_id' => $value,
                    'created_at' => date('Y-m-d H:i:s')
                ];
                $process                 = $this->model->insert('module_letter_incoming_assigned', $data);
                if ($process) {
                    $totalSuccess++;
                }
            }
        }

        if ($totalSuccess > 0) {
            $this->model->update($post['id_letter'], 'module_letter_incoming', [
                'status' => 'assigned'
            ]);
            $result = ['status' => true, 'message' => 'Surat berhasil diassign'];
        }

        return JsonResponse::withJson($response, $result, 200);
    }

    public function delete(Request $request, Response $response, $parameters): Response
    {
        $result = $this->model->deleteData($parameters['id']);
        return JsonResponse::withJson($response, $result, 200);
    }
}
