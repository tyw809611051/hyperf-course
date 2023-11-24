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

namespace App\Component;

use App\Constants\ErrorCode;
use Hyperf\Context\Context;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpMessage\Cookie\Cookie;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

class Response
{
    #[Inject]
    protected ResponseInterface $response;

    /**
     * @param null $data
     */
    public function success($data = null, string $message = 'success'): PsrResponseInterface
    {
        $result = [
            'code' => ErrorCode::SUCCESS,
            'msg' => $message,
            'data' => $data,
        ];
        return $this->response->json($result);
    }

    public function error(int $code = -1, string $message = ''): PsrResponseInterface
    {
        $code = ($code == 0) ? -1 : $code;
        if (empty($message)) {
            $message = ErrorCode::$errorMessages[$code] ?? $message;
        }

        $result = [
            'code' => $code,
            'msg' => $message,
        ];
        return $this->json($result);
    }

    public function json($data): PsrResponseInterface
    {
        return $this->response->json($data);
    }

    public function cookie(Cookie $cookie): static
    {
        $response = $this->response->withCookie($cookie);
        Context::set(PsrResponseInterface::class, $response);
        return $this;
    }

    public function redirect($url, $status = 302): PsrResponseInterface
    {
        return $this->response->redirect($url, $status);
    }
}
