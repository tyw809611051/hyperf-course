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

namespace App\Component\Log;

use RuntimeException;

class RuntimeLog extends Log
{
    public const LOG_NAME = 'runtime';

    public const GROUP = 'runtime';

    public static function __callStatic($name, $arguments)
    {
        if (! method_exists(static::get(self::LOG_NAME, self::GROUP), $name)) {
            throw new RuntimeException('Logger method not found!');
        }
        return call_user_func([self::get(self::LOG_NAME, self::GROUP), $name], ...$arguments);
    }
}
