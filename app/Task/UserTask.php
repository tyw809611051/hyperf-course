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

use App\Component\Server;
use App\Constants\Atomic;
use App\Constants\MemoryTable;
use App\Constants\WsMessage;
use Hyperf\Memory\AtomicManager;
use Hyperf\Memory\TableManager;
use Hyperf\WebSocketServer\Sender;

use Swoole\Table;
use function App\Helper\wsSuccess;

class UserTask
{
    public function unReadApplicationCount(int $fd, $data)
    {
        $result = wsSuccess(WsMessage::WS_MESSAGE_CMD_EVENT, WsMessage::EVENT_GET_UNREAD_APPLICATION_COUNT, $data);
        $sender = \Hyperf\Support\make(Sender::class);
        $sender->push($fd, $result);
    }

    public function setUserStatus(array $fds, array $data)
    {
        if (empty($fds)) {
            return false;
        }
        $result = wsSuccess(WsMessage::WS_MESSAGE_CMD_EVENT, WsMessage::EVENT_USER_STATUS, $data);
        Server::sendToAll($result, $fds);
        return true;
    }

    public function onlineNumber()
    {
        $atomic = AtomicManager::get(Atomic::NAME);

        $userToFdTable = TableManager::get(MemoryTable::USER_TO_FD);

        $fds = [];
        foreach ($userToFdTable as $item) {
            array_push($fds, $item['fd']);
        }

        $data = wsSuccess(
            WsMessage::WS_MESSAGE_CMD_EVENT,
            'onlineNumber',
            "<span>当前在线人数：<b>{$atomic->get()}</b></span>"
        );

        Server::sendToAll($data, $fds);
    }
}
