<?php

declare(strict_types=1);

namespace App\Controller;

use App\Helper\JsonResponse;
use Pimple\Psr11\Container;

use App\Model\BaseModel;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class MasterData
{
    private $container;
    private $auth;
    private $user;
    private $model;
    
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->model                = new BaseModel($this->container->get('db'));
    }

    public function listOrganization(Request $request, Response $response): Response
    {
        $result = ['status' => false, 'message' => 'Data tidak ditemukan', 'data' => array()];
        $list   = $this->model->fetch('module_master_data_organization', true, ['id', 'name']);

        if (!empty($list)) {
            $result = ['status' => true, 'message' => 'Data ditemukan', 'data' => $list];
        }
        
        return JsonResponse::withJson($response, $result, 200);
    }

    public function listPurpose(Request $request, Response $response): Response
    {
        $result = ['status' => false, 'message' => 'Data tidak ditemukan', 'data' => array()];
        $list   = $this->model->fetch('module_master_data_purpose', true, ['id', 'name']);

        if (!empty($list)) {
            $result = ['status' => true, 'message' => 'Data ditemukan', 'data' => $list];
        }
        
        return JsonResponse::withJson($response, $result, 200);
    }
}
