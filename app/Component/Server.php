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

namespace App\Component;

use Hyperf\Utils\ApplicationContext;

class Server
{
    public static function sendToAll($data, array $fds = [])
    {
        /**
         * @var \Swoole\WebSocket\Server $server
         */
        $server = ApplicationContext::getContainer()->get(\Swoole\Server::class);
        foreach ($fds as $fd) {
            if ($server->isEstablished($fd)) {
                $server->push($fd, $data);
            }
        }
    }

    /**
     * Disconnect for client, will trigger onClose.
     *
     * @return bool|mixed
     */
    public static function disconnect(int $fd, int $code = 0, string $reason = '')
    {
        /**
         * @var \Swoole\WebSocket\Server $server
         */
        $server = ApplicationContext::getContainer()->get(\Swoole\Server::class);
        // If it's invalid fd
        if (! $server->isEstablished($fd)) {
            return false;
        }

        return $server->disconnect($fd, $code, $reason);
    }
}
