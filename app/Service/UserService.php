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
use App\Model\UserApplication;
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
     * @return null|\Hyperf\Database\Model\Builder|\Hyperf\Database\Model\Model|object|User
     */
    public static function findUserByEmail(string $email)
    {
        return User::query()->where('email', '=', $email)->first() ?? null;
    }

    /**
     * @return null|\Hyperf\Database\Model\Builder|\Hyperf\Database\Model\Model|object|User
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

    public static function getUserByIds(array $ids)
    {
        return User::query()->whereNull('deleted_at')->whereIn('id', $ids)->get()->toArray();
    }

    public static function getMine(User $userInfo)
    {
        return [
            'username' => $userInfo->username,
            'id' => $userInfo->id,
            'status' => User::STATUS_TEXT[User::STATUS_ONLINE],
            'sign' => $userInfo->sign,
            'avatar' => $userInfo->avatar,
        ];
    }

    /**
     * @return int
     */
    public static function createUserApplication(
        int $userId,
        int $receiverId,
        int $groupId,
        string $applicationType,
        string $applicationReason,
        int $applicationStatus = UserApplication::APPLICATION_STATUS_CREATE,
        int $readState = UserApplication::UN_READ
    ) {
        return UserApplication::query()->insertGetId([
            'uid' => $userId,
            'receiver_id' => $receiverId,
            'group_id' => $groupId,
            'application_type' => $applicationType,
            'application_status' => $applicationStatus,
            'application_reason' => $applicationReason,
            'read_state' => $readState,
        ]);
    }

    public static function findUserInfoById(int $uid)
    {
        $userInfo = User::query()->whereNull('deleted_at')->where(['id' => $uid])->first();
        if (!$userInfo) {
            throw new ApiException(ErrorCode::USER_NOT_FOUND);
        }

        return $userInfo;
    }
}
