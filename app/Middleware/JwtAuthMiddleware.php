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

namespace App\Middleware;

use App\Component\Response;
use App\Constants\ErrorCode;
use App\Exception\ApiException;
use App\Model\User;
use Hyperf\Context\Context;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\ResponseInterface as HttpResponse;
use Phper666\JwtAuth\Jwt;
use Phper666\JWTAuth\Util\JWTUtil;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class JwtAuthMiddleware implements MiddlewareInterface
{
    #[Inject]
    protected Response $resp;

    protected string $prefix = 'Bearer';

    #[Inject]
    protected JWT $jwt;

    //    public function __construct(HttpResponse $response, Jwt $jwt)
    //    {
    //        $this->response = $response;
    //        $this->jwt = $jwt;
    //    }

    /**
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $tokenAll = $request->getHeader('Authorization')[0] ?? '';
        $token = "";
        if ($tokenAll) {
            $arr = explode($this->prefix . ' ', $tokenAll);
            $token = $arr[1] ?? '';
        }

        if (empty($token)) {
            $token = $request->getCookieParams()['IM_TOKEN'] ?? '';
        }
        if (empty($token)) {
            $token = $request->getQueryParams()['token'] ?? '';
        }

        if (empty($token)) {
            return $this->resp->redirect('/index/login');
        }

        $request = $request->withAddedHeader('Authorization', 'Bearer ' . $token);
        var_dump($token);
        $jwtData = JWTUtil::getParserData($request);
        $user = User::query()->where(['id' => $jwtData['uid']])->first();
        if (empty($user)) {
            throw new ApiException(ErrorCode::AUTH_ERROR);
        }
        $request = Context::get(ServerRequestInterface::class);
        $request = $request->withAttribute('user', $user);
        Context::set(ServerRequestInterface::class, $request);

        return $handler->handle($request);
        return $this->resp->redirect('/index/login');
    }
}
