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
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\RequestMapping;

#[AutoController(prefix: "index")]
class IndexController extends CommonController
{

    #[RequestMapping(path: "login",methods: "get")]
    public function login()
    {
        return $this->view->render('user/login');
    }

    /**
     * @RequestMapping(path="register",methods="GET")
     */
    public function register()
    {
        return $this->view->render('user/register');
    }
}