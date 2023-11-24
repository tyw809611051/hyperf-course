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

namespace App\Service;

use App\Constants\ErrorCode;
use App\Exception\ApiException;
use App\Model\User;
use App\Model\UserLoginLog;

use function App\Helper\getClientIp;

class UserService
{
    public static function register(string $email, string $password): bool
    {
        $user = self::findUserByEmail($email);
        if ($user) {
            throw new ApiException(ErrorCode::USER_EMAIL_ALREADY_USE);
        }
        return User::query()->insert([
            'email' => $email,
            'password' => password_hash($password, CRYPT_BLOWFISH),
            'username' => $email,
            'sign' => '',
            'status' => User::STATUS_OFFLINE,
            'avatar' => 'https://cdn.sep.cc/avatar/',
            'created_at' => date('Y-m-d H:i:s', time()),
        ]);
    }

    public static function userLoginLog(int $uid): bool
    {
        return UserLoginLog::query()->insert([
            'uid' => $uid,
            'user_login_ip' => intval(sprintf('%u', ip2long(getClientIp()))),
            'created_at' => date('Y-m-d H:i:s', time()),
        ]);
    }

    /**
     * @return null|User|\Hyperf\Database\Model\Builder|\Hyperf\Database\Model\Model|object
     */
    public static function findUserByEmail(string $email)
    {
        return User::query()->where('email', '=', $email)->first() ?? null;
    }

    /**
     * @return null|User|\Hyperf\Database\Model\Builder|\Hyperf\Database\Model\Model|object
     */
    public static function findUserById(int $uid)
    {
        return User::query()->find($uid) ?? null;
    }

    public static function login(string $email, string $password): User
    {
        $user = self::findUserByEmail($email);
        if (! $user || $user['delete_at'] !== null) {
            throw new ApiException(ErrorCode::USER_NOT_FOUND);
        }
        if (! password_verify($password, $user['password'])) {
            throw new ApiException(ErrorCode::USER_PASSWORD_ERROR);
        }

        self::userLoginLog($user['id']);
        return $user;
    }
}
