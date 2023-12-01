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

use App\Component\MessageParser;
use App\Component\WsProtocol;
use App\Constants\Atomic;
use App\Constants\MemoryTable;
use App\Controller\AbstractController;
use App\Model\User;
use App\Service\UserService;
use App\Task\UserTask;
use Hyperf\Context\Context;
use Hyperf\Contract\OnCloseInterface;
use Hyperf\Contract\OnMessageInterface;
use Hyperf\Contract\OnOpenInterface;
use Hyperf\Engine\WebSocket\Opcode;
use Hyperf\HttpServer\Router\Dispatched;
use Hyperf\HttpServer\Router\DispatcherFactory;
use Hyperf\Memory\AtomicManager;
use Hyperf\Memory\TableManager;
use Hyperf\WebSocketServer\Context as WsContext;

class WebSocketController extends AbstractController implements OnMessageInterface, OnOpenInterface, OnCloseInterface
{
    public function onMessage($server, $frame): void
    {
        // TODO: Implement onMessage() method.
        if ($frame->opcode == Opcode::PING) {
            $server->push('', Opcode::PONG);
            return;
        }
        //        $server->push($frame->fd, 'Recv: ' . $frame->data);
        // 处理消息
        $message = MessageParser::decode($frame->data);
        Context::set('request', new WsProtocol(
            $message['data'],
            $message['ext'],
            $frame->fd,
            $server->getClientInfo($frame->fd)['last_time'] ?? 0
        ));
        $dispatcher = $this->container
            ->get(DispatcherFactory::class)
            ->getDispatcher('ws');
        $controller = explode('.', $message['cmd'])[0] ?? '';
        $method = explode('.', $message['cmd'])[1] ?? '';
        $dispatched = make(Dispatched::class, [
            $dispatcher->dispatch('GET', sprintf('/%s/%s', $controller, $method)),
        ]);
        if ($dispatched->isFound()) {
            // 路由处理
            $result = call_user_func([
                make($dispatched->handler->callback[0]),
                $dispatched->handler->callback[1],
            ]);
            if ($result !== null) {
                $receive = [
                    'cmd' => $message['cmd'],
                    'data' => $result,
                    'ext' => [],
                ];
                //                $this->sender->push($frame->fd, MessageParser::encode($receive));
                $server->push($frame->fd, MessageParser::encode($receive));
            }
        }
        var_dump('onMessage', $frame->data);
    }

    public function onOpen($server, $request): void
    {
        // TODO: Implement onOpen() method.
        /**
         * @var \App\Model\User $user
         */
        $user = WsContext::get('user');
        $checkOnline = TableManager::get(MemoryTable::USER_TO_FD)->get((string) $user->id, 'fd');

        if ($checkOnline) {
            \App\Component\Server::disconnect($checkOnline, 0, '你的帐号在别的地方登录!');
        }

        TableManager::get(MemoryTable::FD_TO_USER)->set((string) $request->fd, ['userId' => $user->id]);
        TableManager::get(MemoryTable::USER_TO_FD)->set((string) $user->id, ['fd' => $request->fd]);

        // TODO 保存用户状态
        UserService::setUserStatus($user->id, User::STATUS_ONLINE);

        $atomic = AtomicManager::get(Atomic::NAME);
        $atomic->add(1);

        $task = $this->container->get(UserTask::class);
        $task->onlineNumber();
        var_dump('onOpen');
    }

    public function onClose($server, int $fd, int $reactorId): void
    {
        // TODO: Implement onClose() method.
        $userId = TableManager::get(MemoryTable::FD_TO_USER)->get((string) $fd, 'userId');
        $selfFd = TableManager::get(MemoryTable::USER_TO_FD)->get((string) $userId, 'fd');

        if ($fd == $selfFd) {
            TableManager::get(MemoryTable::USER_TO_FD)->del((string) $userId);
            TableManager::get(MemoryTable::FD_TO_USER)->del((string) $fd);
        }

        UserService::setUserStatus($userId, User::STATUS_OFFLINE);

        $atomic = AtomicManager::get(Atomic::NAME);
        $atomic->sub(1);

        WsContext::destroy('user');
        $this->container->get(UserTask::class)->onlineNumber();
        var_dump('onClose');
    }
}
