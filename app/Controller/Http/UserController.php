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

use App\Component\Response;
use App\Constants\ErrorCode;
use App\Controller\AbstractController;
use App\Exception\ApiException;
use App\Exception\InputException;
use App\Service\UserService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpMessage\Cookie\Cookie;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;
use InvalidArgumentException;
use Phper666\JWTAuth\JWT;
use function App\Helper\jsonError;
use function App\Helper\jsonSuccess;
use Psr\Http\Message\ResponseInterface;

#[AutoController(prefix: "user")]
class UserController extends CommonController
{

    #[Inject]
    private JWT $jwt;

    #[Inject]
    protected ValidatorFactoryInterface $validationFactory;

    /**
     * 登录
     * @return ResponseInterface
     * @throws InvalidArgumentException
     *
     */
    #[RequestMapping(path: "login",methods: "POST")]
    public function login(): ResponseInterface
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

            return $this->resp->success([
                'token'=>$token,
                'uid'=>$user->id,
            ]);
        } catch (\Exception $e) {
            return $this->resp->error($e->getCode());
        }
    }

    /**
     * 注册
     * @return ResponseInterface
     * */
    #[PostMapping(path: "register")]
    public function register(): ResponseInterface
    {
        $email = $this->request->input('email');
        $password = $this->request->input('password');
        $params = $this->request->all();
        //---------参数校验---------//
        $validator = $this->validationFactory->make($params,[
            'email'    => 'required|email|max:50',
            'password' => 'required|string|max:50',
        ]);
        try {
            if ($validator->fails()) {
                $errMsg = array_values($validator->errors()->all());

                throw new ApiException(ErrorCode::PARAM_ERROR,implode(',', $errMsg));
            }
            $regRs = UserService::register($email,$password);
        } catch (\Exception $e) {
            return $this->resp->error(intval($e->getCode()));
        }

        if (!$regRs) {
            return $this->resp->error(ErrorCode::USER_EMAIL_ALREADY_USE);
        }
        return $this->resp->success();
    }

}