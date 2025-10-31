<?php

declare(strict_types=1);

namespace App\Controller;

use App\Helper\JsonResponse;
use Pimple\Psr11\Container;

use App\Model\GuestBookModel;
use App\Helper\FileUpload;
use App\Helper\General;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class Guest
{
    private $container;
    private $auth;
    private $model;
    private $general;
    private $file;
    
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->model                = new GuestBookModel($this->container->get('db'));
        $this->file                 = new FileUpload($this->container);
        $this->general              = new General($this->container);
    }

    public function register(Request $request, Response $response): Response
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
    
    public function detailEvent(Request $request, Response $response, $parameters): Response
    {
        $result = ['status' => false, 'message' => 'Data tidak ditemukan', 'data' => array()];

        $data = $this->model->detailEvent($parameters['id']);
        if (!empty($data)) {
            $result = ['status' => true, 'message' => 'Data berhasil ditemukan', 'data' => $data];
        }

        return JsonResponse::withJson($response, $result, 200);
    }

    public function registerEvent(Request $request, Response $response): Response
    {
        $post                   = $request->getParsedBody();
        $data                   = [
            'id' => $post['id'] ?? null,
            'name' => $post['name'],
            'email' => $post['email'],
            'phone' => $post['phone'],
            'organization_id' => $post['organization_id'],
            'address' => $post['address'],
            'event_id' => $post['event_id'],
        ];

        $result                 = $this->model->registerEvent($data);

        return JsonResponse::withJson($response, $result, 200);
    }
}
