<?php

declare(strict_types=1);

namespace App\Controller;

use App\Helper\JsonResponse;
use Pimple\Psr11\Container;

use App\Model\AuthModel;
use App\Model\UserModel;

use App\Helper\General;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

final class User
{
    private $container;
    private $auth;
    private $user;
    private $userModel;
    private $general;
    
    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->auth                 = new AuthModel($this->container->get('db'));
        $this->general              = new General($this->container);
        $this->user                 = $this->auth->validateToken();

        $roles                      = array('superadmin','admin','teacher');

        if(!in_array($this->user->role, $roles)) {
            $this->auth->denyAccess();
        }

        $this->userModel            = new UserModel($this->container->get('db'));
    }

    public function info(Request $request, Response $response): Response
    {
        $data = $this->user;

        $detailUser         = $this->auth->fetchBy($this->user->id, 'users');

        $result['status']   = true;
        $result['data']     = $this->general->replaceNullInArray((array) $data);

        return JsonResponse::withJson($response, $result, 200);
    }

    public function changePassword(Request $request, Response $response): Response
    {
        $post                       = $request->getParsedBody();
        $new_password               = isset($post["new_password"]) ? $post["new_password"] : '';
        $confirm_password           = isset($post["confirm_password"]) ? $post["confirm_password"] : '';

        $result = ['status' => false, 'message' => 'Password gagal diperharui!'];

        if ($new_password == $confirm_password) {
            $process     = $this->userModel->updatePassword($this->user->id, $new_password);
            
            if ($process) {
                $result      = ['status' => true, 'message' => 'Password berhasil diperharui!'];
            }
        }


        return JsonResponse::withJson($response, $result, 200);
    }
}
