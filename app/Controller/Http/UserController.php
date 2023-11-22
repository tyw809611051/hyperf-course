<?php
declare(strict_types = 1);

/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf-cloud/hyperf/blob/master/LICENSE
 */

namespace App\Controller\Http;

use App\Controller\AbstractController;
use App\Service\UserService;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\RequestMapping;

#[AutoController(prefix: "user")]
class UserController extends AbstractController
{

    /**
     * 登录
     * @throws \InvalidArgumentException
     * */
    #[RequestMapping(path: "login",methods: "POST")]
    public function login()
    {
        $email = $this->request->input('email');
        $password   = $this->request->input('password');
        $user  = UserService::login($email,$password);
        $auth = [
            'uid'=>$user->id,
            'username'=>$user->email,
        ];
//        $token =
    }
}