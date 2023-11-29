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

use App\Controller\AbstractController;
use Hyperf\Contract\OnCloseInterface;
use Hyperf\Contract\OnMessageInterface;
use Hyperf\Contract\OnOpenInterface;
use Hyperf\Engine\WebSocket\Opcode;

class WebSocketController extends AbstractController implements OnMessageInterface, OnOpenInterface, OnCloseInterface
{
    public function onMessage($server, $frame): void
    {
        // TODO: Implement onMessage() method.
        if ($frame->opcode == Opcode::PING) {
            $server->push('', Opcode::PONG);
            return;
        }
        $server->push($frame->fd, 'Recv: ' . $frame->data);
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
