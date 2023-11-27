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

use App\Component\Response;
use App\Controller\AbstractController;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Contract\RequestInterface;

class CommonController extends AbstractController
{
    #[Inject]
    protected Response $resp;

    #[Inject]
    protected RequestInterface $request;

    public function __construct()
    {
        var_dump(1111111);
        $cookie = $this->request->cookie('Authorization', '');
        var_dump('request cookie', $cookie);
        if ($cookie) {
            $this->request->withAddedHeader('Authorization', $cookie);
        }
    }
}
