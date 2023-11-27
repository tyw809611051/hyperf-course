<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */

namespace App\Controller\Http;

use App\Constants\ErrorCode;
use App\Exception\ApiException;
use App\Service\UserService;
use Exception;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpMessage\Cookie\Cookie;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\Logger\LoggerFactory;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;
use InvalidArgumentException;
use Phper666\JWTAuth\JWT;
use Phper666\JWTAuth\Util\JWTUtil;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

#[AutoController(prefix: 'user')]
class UserController extends CommonController
{
    #[Inject]
    protected ValidatorFactoryInterface $validationFactory;

    protected LoggerInterface $logger;

    #[Inject]
    private JWT $jwt;

    public function __construct(LoggerFactory $loggerFactory)
    {
        // 第一个参数对应日志的 name, 第二个参数对应 config/autoload/logger.php 内的 key
        $this->logger = $loggerFactory->get('log', 'default');
    }

    /**
     * 登录.
     * @throws InvalidArgumentException
     */
    #[RequestMapping(path: 'login', methods: 'POST')]
    public function login(): ResponseInterface
    {
        $email = $this->request->input('email');
        $password = $this->request->input('password');
        try {
            $user = UserService::login($email, $password);
            $auth = [
                'uid' => $user->id,
                'username' => $user->email,
            ];
            $token = $this->jwt->getToken('default', $auth);

            $cookie = new Cookie('IM_TOKEN', $token->toString(), $this->jwt->getTTL($token->toString()) * 1000, '/');
            //            return $this->resp->cookie($cookie)->success([
            //                'token' => $token->toString(),
            //                'exp' => $this->jwt->getTTL($token->toString()),
            //                'uid' => $user->id,
            //            ]);
            //            return $this->resp->cookie($cookie)->redirect('user/home')
            //                ->withAddedHeader('Authorization', 'Bearer ' . $token->toString());
            return $this->response->withCookie($cookie)->redirect('/user/home', 302, 'https')
                ->withAddedHeader('Authorization', 'Bearer ' . $token->toString());
        } catch (Exception $e) {
            return $this->resp->error(intval($e->getCode()), $e->getMessage());
        }
    }

    /**
     * 注册.
     * */
    #[PostMapping(path: 'register')]
    public function register(): ResponseInterface
    {
        $email = $this->request->input('email');
        $password = $this->request->input('password');
        $params = $this->request->all();
        // ---------参数校验---------//
        $validator = $this->validationFactory->make($params, [
            'email' => 'required|email|max:50',
            'password' => 'required|string|max:50',
        ]);
        try {
            if ($validator->fails()) {
                $errMsg = array_values($validator->errors()->all());

                throw new ApiException(ErrorCode::PARAM_ERROR, implode(',', $errMsg));
            }
            $regRs = UserService::register($email, $password);
        } catch (Exception $e) {
            return $this->resp->error(intval($e->getCode()), $e->getMessage());
        }

        if (! $regRs) {
            return $this->resp->error(ErrorCode::USER_EMAIL_ALREADY_USE);
        }
        return $this->resp->success();
    }

    /**
     * home.
     * */
    #[RequestMapping(path: 'home', methods: 'GET')]
    public function home(): ResponseInterface
    {
        try {
            $jwtData = JWTUtil::getParserData($this->request);
            $this->logger->info('user: ' . json_encode($jwtData), []);
            if (! $jwtData) {
                return $this->resp->redirect(env('APP_URL') . '/index/login');
            }
        } catch (Exception $e) {
            $this->logger->info('user exception: ' . $e->getMessage(), []);
            return $this->resp->redirect(env('APP_URL') . '/index/login');
        }

        $menus = \Hyperf\Config\Config('menu');
        $user = UserService::findUserById($jwtData['uid']);
        return $this->view->render('user/home', [
            'menus' => $menus,
            'user' => $user,
            'wsUrl' => env('WS_URL'),
            'webRtcUrl' => env('WEB_RTC_URL'),
            'stunServer' => 'stunServer',
        ]);
    }
}
