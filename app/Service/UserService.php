<?php
declare(strict_types = 1);

/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf-cloud/hyperf/blob/master/LICENSE
 */

namespace App\Service;

use App\Constants\ErrorCode;
use App\Exception\ApiException;
use App\Model\User;
use App\Model\UserLoginLog;
use function App\Helper\getClientIp;


class UserService
{
    /**
     * @param int $uid
     *
     * @return bool
     * */
    public static function userLoginLog(int $uid) :bool
    {
        return UserLoginLog::query()->insert([
            'uid'=>$uid,
            'user_login_ip'=>getClientIp(),
        ]);
    }

    /**
     * @param string $email
     *
     * @return null|\App\Model\User|\Hyperf\Database\Model\Builder|\Hyperf\Database\Model\Model|object
     */
    public static function findUserByEmail(string $email)
    {
        return User::query()->where('email','=',$email)->first() ?? NULL;
    }

    /**
     * @param string $email
     * @param string $password
     *
     * @return User
     * */
    public static function login(string $email, string $password) :User
    {

        $user = self::findUserByEmail($email);
        if (!$user || $user['delete_at'] !== NULL) {
            throw new ApiException(ErrorCode::USER_NOT_FOUND);
        }
        if (!password_verify($password,$user['password'])) {
            throw new ApiException(ErrorCode::USER_PASSWORD_ERROR);
        }

        self::userLoginLog($user['id']);
        return $user;
    }
}