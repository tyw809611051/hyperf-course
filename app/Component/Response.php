<?php
declare(strict_types = 1);
namespace App\Component;

use App\Constants\ErrorCode;
use Hyperf\Codec\Json;
use Hyperf\HttpMessage\Stream\SwooleStream;
use Hyperf\HttpServer\Exception\Http\EncodingException;
use Hyperf\HttpServer\Response as HyperfResponse;
use Hyperf\Utils\Contracts\Arrayable;
use Hyperf\Utils\Contracts\Jsonable;
use Psr\Http\Message\ResponseInterface;

class Response extends HyperfResponse
{
    /**
     * @param null $data
     * @param int $code
     * @param string $message
     * @return ResponseInterface
     */
    public function success($data = NULL, int $code = 0, string $message = 'success'): ResponseInterface
    {
        $result = [
            'code'    => $code,
            'msg' => $message,
            'data'    => $data
        ];
        return $this->json($result);
    }

    /**
     *
     * @param int $code
     * @param string $message
     * @return ResponseInterface
     */
    public function error(int $code = -1, string $message = ''): ResponseInterface
    {
        $code   = ($code == 0) ? -1 : $code;
        $msg    = ErrorCode::$errorMessages[$code] ?? $message;
        $result = [
            'code'    => $code,
            'msg' => $msg,
        ];
        return $this->json($result);
    }

    /**
     * @param array|Arrayable|Jsonable $result
     *
     * @param int $statusCode
     *
     * @param int $options
     * @return ResponseInterface
     */
    public function json($result, int $statusCode = 200, $options = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES): ResponseInterface
    {
        $data = $this->toJson($result);
        return $this->getResponse()
            ->withStatus($statusCode)
            ->withAddedHeader('content-type', 'application/json; charset=utf-8')
            ->withBody(new SwooleStream($data));
    }

    /**
     * @param array|Arrayable|Jsonable $data
     * @param int $options
     *
     * @return string
     */
    protected function toJson($data, int $options = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : string
    {
        try {
            $result = Json::encode($data, $options);
        } catch (\Throwable $exception) {
            throw new EncodingException($exception->getMessage(), $exception->getCode());
        }

        return $result;
    }

    /**
     * @param string $xml
     * @param int $statusCode
     *
     * @return ResponseInterface
     */
    public function toWechatXML(string $xml, int $statusCode = 200): ResponseInterface
    {
        return $this->getResponse()
            ->withStatus($statusCode)
            ->withAddedHeader('content-type', 'application/xml; charset=utf-8')
            ->withBody(new SwooleStream($xml));
    }
}