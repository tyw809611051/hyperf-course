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
use App\Middleware\JwtAuthMiddleware;
use App\Service\FriendService;
use App\Service\UserService;
use Exception;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpMessage\Cookie\Cookie;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\PostMapping;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\Logger\LoggerFactory;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;
use InvalidArgumentException;
use Phper666\JWTAuth\JWT;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

use function App\Helper\checkAuth;

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
        $cookie = $this->request->cookie('IM_TOKEN', '');
        if ($cookie) {
            $this->request->withAddedHeader('Authorization', 'Bearer ' . $cookie);
        }
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
            var_dump('login-token', $token->toString());
            return $this->resp->cookie($cookie)->success([
                'token' => $token->toString(),
                'exp' => $this->jwt->getTTL($token->toString()),
                'uid' => $user->id,
            ]);
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
            $user = checkAuth();
            if (! $user) {
                return $this->resp->redirect(env('APP_URL') . '/index/login');
            }
        } catch (Exception $e) {
            $this->logger->info('user exception: ' . $e->getMessage(), []);
            return $this->resp->redirect(env('APP_URL') . '/index/login');
        }

        $menus = \Hyperf\Config\Config('menu');
        return $this->view->render('user/home', [
            'menus' => $menus,
            'user' => $user,
            'wsUrl' => env('WS_URL'),
            'webRtcUrl' => env('WEB_RTC_URL'),
            'stunServer' => 'stunServer',
        ]);
    }

    #[RequestMapping(path: 'init', methods: 'GET')]
    #[Middleware(JwtAuthMiddleware::class)]
    public function init(): ResponseInterface
    {
        $user = $this->request->getAttribute('user');
        $friend = FriendService::getFriend($user->id);
        $group = FriendService::getGroup($user->id);
        return $this->resp->success([
            'mine' => UserService::getMine($user),
            'friend' => $friend,
            'group' => $group,
        ]);
    }

    #[RequestMapping(path: 'signOut', methods: 'GET')]
    public function signOut()
    {
        $token = $this->request->getCookieParams()['IM_TOKEN'] ?? '';
        $this->jwt->logout($token);
        return $this->response->withCookie(new Cookie('IM_TOKEN', ''))->redirect(env('APP_URL') . '/index/login');
    }

    #[RequestMapping(path: 'applyList', methods: 'GET')]
    #[Middleware(JwtAuthMiddleware::class)]
    public function applyList()
    {
        $user = $this->request->getAttribute('user');
        $page = $this->request->input('page');
        $size = $this->request->input('size',10);
        $result = UserService::applyList($user->id, (int) $page, (int) $size);
        return $this->resp->success($result);
    }

    #[RequestMapping(path: 'agreeFriend', methods: 'POST')]
    public function agreeFriend()
    {
        $id = $this->request->input('id');
        $uid = $this->request->input('uid'); // 对方用户ID
        $fromGroup = $this->request->input('from_group'); // 对方设定的好友分组
        $group = $this->request->input('group'); // 我设定的好友分组
        $result = UserService::agreeFriend(intval($id), intval($group));
        return $this->resp->success($result);
    }

    #[RequestMapping(path: 'refuseFriend', methods: 'POST')]
    public function refuseFriend()
    {
        $id = $this->request->input('id');
        $result = UserService::refuseFriend(intval($id));
        return $this->resp->success($result);
    }
}
