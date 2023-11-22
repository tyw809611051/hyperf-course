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
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;

/**
 * Class IndexController
 * @package App\Controller
 * @Controller(prefix="index")
 */
class IndexController extends AbstractController
{

    /**
     * @RequestMapping(path="login",methods="GET")
     */
    public function login()
    {
        return $this->view->render('user/login');
    }
}