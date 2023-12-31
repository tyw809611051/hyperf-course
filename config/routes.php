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
use App\Middleware\AuthMiddleware;
use Hyperf\HttpServer\Router\Router;

Router::addRoute(['GET', 'POST', 'HEAD'], '/', 'App\Controller\Http\HomeController@index');

Router::addServer('ws', function () {
    Router::get('/im', 'App\Controller\Ws\WebSocketController', ['middleware' => [AuthMiddleware::class]]);
    Router::get('/video', 'App\Controller\Ws\VideoController');
});
