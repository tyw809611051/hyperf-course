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

use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Psr\Http\Message\ResponseInterface;

#[AutoController(prefix: 'index')]
class IndexController extends CommonController
{
    #[RequestMapping(path: 'login', methods: 'get')]
    public function login(): ResponseInterface
    {
        return $this->view->render('user/login');
    }

    #[RequestMapping(path: 'register', methods: 'GET')]
    public function register(): ResponseInterface
    {
        return $this->view->render('user/register');
    }

    #[RequestMapping(path: 'createFriendGroup', methods: 'GET')]
    public function createFriendGroup(): ResponseInterface
    {
        return $this->view->render('friend/createGroup');
    }

    #[RequestMapping(path: 'findUser', methods: 'GET')]
    public function findUser()
    {
        return $this->view->render('friend/find');
    }

    #[RequestMapping(path: 'createGroup', methods: 'GET')]
    public function createGroup()
    {
        return $this->view->render('group/create');
    }

    #[RequestMapping(path: 'findGroup', methods: 'GET')]
    public function findGroup()
    {
        return $this->view->render('group/find');
    }
}
