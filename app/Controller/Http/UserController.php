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
use App\Exception\InputException;
use App\Service\UserService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpMessage\Cookie\Cookie;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;
use Laminas\Stdlib\ResponseInterface;
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
            return $this->response->error($e->getCode());
        }
    }

    /**
     * 注册
     * @return \Psr\Http\Message\ResponseInterface
     * */
    #[PostMapping(path: "register")]
    public function register()
    {
        $email = $this->request->input('email');
        $password = $this->request->input('password');
        $params = $this->request->all();
        //---------参数校验---------//
        $validator = $this->validationFactory->make($params,[
            'email'    => 'required|email|max:50',
            'password' => 'required|string|max:50',
        ]);
        if ($validator->fails()) {
            $errMsg = array_values($validator->errors()->all());
            throw new InputException(implode(',', $errMsg));
        }
        return $this->response->success(UserService::register($email, $password));
    }

}