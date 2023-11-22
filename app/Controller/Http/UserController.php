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
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpMessage\Cookie\Cookie;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;
use Phper666\JWTAuth\JWT;
use function App\Helper\jsonError;
use function App\Helper\jsonSuccess;

#[AutoController(prefix: "user")]
class UserController extends AbstractController
{

    #[Inject]
    private JWT $jwt;

    #[Inject]
    protected ValidatorFactoryInterface $validationFactory;

    /**
     * 登录
     * @throws \InvalidArgumentException
     * */
    #[RequestMapping(path: "login",methods: "POST")]
    public function login()
    {
        $email = $this->request->input('email');
        $password   = $this->request->input('password');
        try {
            $user  = UserService::login($email,$password);
            $auth = [
                'uid'=>$user->id,
                'username'=>$user->email,
            ];
            $token = $this->jwt->getToken($auth);
            return $this->response
                ->withCookie(new Cookie('IM_TOKEN', (string)$token, time() + $this->jwt->getTTL(), '/', '', false, false))
                ->json(jsonSuccess([
                    'token'=>$token,
                    'uid'=>$user->id,
                ]));
        } catch (\Exception $e) {
            return $this->response->json(jsonError($e->getMessage()));
        }

    }
}