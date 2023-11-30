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

namespace App\Controller\Ws;

use App\Component\WsProtocol;
use App\Constants\MemoryTable;
use App\Controller\AbstractController;
use App\Service\UserService;
use App\Task\UserTask;
use Hyperf\Context\Context;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\Memory\TableManager;

class UserController extends AbstractController
{
    #[RequestMapping(path: 'ping', methods: 'GET')]
    public function index()
    {
        return WEBSOCKET_OPCODE_PONG;
    }

    #[RequestMapping(path: 'getUnreadApplicationCount', methods: 'GET')]
    public function getUnreadApplicationCount()
    {
        /**
         * @var WsProtocol $protocol
         * */
        $protocol = Context::get('request');
        $userId = TableManager::get(MemoryTable::FD_TO_USER)->get((string) $protocol->getFd(), 'userId') ?? '';
        $count = UserService::getUnreadApplicationCount($userId);

        $this->container->get(UserTask::class)->unReadApplicationCount($protocol->getFd(), $count);
    }
}
