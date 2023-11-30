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
use App\Controller\AbstractController;
use Hyperf\Context\Context;
use Hyperf\Contract\OnCloseInterface;
use Hyperf\Contract\OnMessageInterface;
use Hyperf\Contract\OnOpenInterface;
use Hyperf\Engine\WebSocket\Opcode;
use Hyperf\HttpServer\Router\Dispatched;
use Hyperf\HttpServer\Router\DispatcherFactory;
use Hyperf\Di\Annotation\Inject;
use Hyperf\WebSocketServer\Sender;

class WebSocketController extends AbstractController implements OnMessageInterface, OnOpenInterface, OnCloseInterface
{
    #[Inject]
    protected $sender;

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
                $this->sender->push($frame->fd, MessageParser::encode($receive));
            }
        }
        var_dump('onMessage', $frame->data);
    }

    public function onOpen($server, $request): void
    {
        // TODO: Implement onOpen() method.
        $server->push($request->fd, 'Opened');
        var_dump('onOpen');
    }

    public function onClose($server, int $fd, int $reactorId): void
    {
        // TODO: Implement onClose() method.
        var_dump('onClose');
    }
}
