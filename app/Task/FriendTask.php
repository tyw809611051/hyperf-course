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
use Hyperf\Di\Annotation\Inject;
use Hyperf\Task\Annotation\Task;

use function App\Helper\wsSuccess;

class FriendTask
{
    #[Inject]
    protected \Hyperf\WebSocketServer\Sender $sender;

    /**
     * @Task
     */
    public function agreeApply(int $fd, array $data)
    {
        $result = wsSuccess(WsMessage::WS_MESSAGE_CMD_EVENT, WsMessage::EVENT_FRIEND_AGREE_APPLY, $data);
        $this->sender->push($fd, $result);
    }

    /**
     * @Task
     *
     * @param mixed $fd
     * @param mixed $username
     * @param mixed $avatar
     * @param mixed $userId
     * @param mixed $type
     * @param mixed $content
     * @param mixed $cid
     * @param mixed $mine
     * @param mixed $fromId
     * @param mixed $timestamp
     * @return bool
     */
    public function sendMessage(
        $fd,
        $username,
        $avatar,
        $userId,
        $type,
        $content,
        $cid,
        $mine,
        $fromId,
        $timestamp
    ) {
        var_dump('task sendMessage', $fd);
        if (! $fd) {
            return false;
        }
        $data = [
            'username' => $username,
            'avatar' => $avatar,
            'id' => $userId,
            'type' => $type,
            'content' => $content,
            'cid' => $cid,
            'mine' => $mine,
            'fromid' => $fromId,
            'timestamp' => $timestamp,
        ];
        $result = wsSuccess(WsMessage::WS_MESSAGE_CMD_EVENT, WsMessage::EVENT_GET_MESSAGE, $data);
        $this->sender->push($fd, $result);
    }
}
