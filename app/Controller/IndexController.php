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
namespace App\Controller;

use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\View\RenderInterface;

class IndexController extends AbstractController
{
    /*
     * @AutoController
     * */
    #[\ReturnTypeWillChange]
    public function index(RenderInterface $render)
    {
        $user = $this->request->input('user', 'Hyperf');
        $method = $this->request->getMethod();

        return $render->render('index');
//        return $this->view->render('index',['name' => $user]);
//        return [
//            'method' => $method,
//            'message' => "Hello {$user}.",
//        ];
    }

   // 在参数上通过定义 RequestInterface 和 ResponseInterface 来获取相关对象，对象会被依赖注入容器自动注入
    public function course(RequestInterface $request, RenderInterface $render)
    {
        $target = $request->input('target', 'World');
//        return $render->render('index', ['name' => $target]);
        return [
            'method' => $target,
            'message' => "Hello {$target}.",
        ];
    }

}
