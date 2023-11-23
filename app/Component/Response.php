<?php
declare(strict_types = 1);
namespace App\Component;

use App\Constants\ErrorCode;
use Hyperf\Di\Annotation\Inject;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;

class Response
{

    #[Inject]
    protected ResponseInterface $response;

    /**
     * @param null $data
     * @param string $message
     * @return PsrResponseInterface
     */
    public function success($data = NULL, string $message = 'success'): PsrResponseInterface
    {
        $result = [
            'code'    => ErrorCode::SUCCESS,
            'msg' => $message,
            'data'    => $data
        ];
        return $this->response->json($result);
    }

    /**
     *
     * @param int $code
     * @param string $message
     * @return PsrResponseInterface
     */
    public function error(int $code = -1, string $message = ''): PsrResponseInterface
    {
        $code   = ($code == 0) ? -1 : $code;
        if (empty($message)) {
            $message    = ErrorCode::$errorMessages[$code] ?? $message;
        }

        $result = [
            'code'    => $code,
            'msg' => $message,
        ];
        return $this->json($result);
    }

    /**
     * @param $data
     *
     * @return PsrResponseInterface
     */
    public function json($data): PsrResponseInterface
    {
        return $this->response->json($data);
    }
}