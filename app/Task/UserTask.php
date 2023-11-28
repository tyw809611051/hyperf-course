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

namespace App\Task;

use App\Constants\WsMessage;
use Hyperf\WebSocketServer\Sender;

use function App\Helper\wsSuccess;

class UserTask
{
    public function unReadApplicationCount(int $fd, $data)
    {
        $result = wsSuccess(WsMessage::WS_MESSAGE_CMD_EVENT, WsMessage::EVENT_GET_UNREAD_APPLICATION_COUNT, $data);
        $sender = \Hyperf\Support\make(Sender::class);
        $sender->push($fd, $result);
    }
}
