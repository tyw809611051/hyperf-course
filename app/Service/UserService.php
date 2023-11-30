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
use App\Model\FriendRelation;
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
        if (! $userInfo) {
            throw new ApiException(ErrorCode::USER_NOT_FOUND);
        }

        return $userInfo;
    }

    public static function getUnreadApplicationCount(int $uid)
    {
        return UserApplication::query()
            ->whereNull('deleted_at')
            ->where('read_state', 'eq', userApplication::UN_READ)
            ->where('receiver_id', '=', $uid)
            ->count('id');
    }

    public static function applyList(int $uid, int $page, int $size)
    {
        $list = UserApplication::query()->whereNull('deleted_at')
            ->where(['uid' => $uid])
            ->orWhere(['receiver_id' => $uid])
            ->orderBy('created_at', 'desc')
            ->limit($size)->offset(($page - 1) * $size)
            ->get()->toArray();

        $rtList = [];
        foreach ($list as $k => $item) {
            $time = $item['created_at'];
            switch ($item['application_type']) {
                case UserApplication::APPLICATION_CREATE_USER:
                    break;
                case UserApplication::APPLICATION_RECEIVER_USER:
                    break;
                case UserApplication::APPLICATION_SYSTEM:
                    break;
                case UserApplication::APPLICATION_TYPE_FRIEND:
                    $type = 1;
                    switch ($item['application_status']) {
                        case 0:
                            if ($item['receiver_id'] == $uid) {
                                $content = '申请加你好友';
                                $from = $item['uid'];
                                $fromGroup = $item['group_id'];
                                $user = User::find($item['uid']);
                                $remark = $item['application_reason'];
                            } else {
                                $content = UserApplication::APPLICATION_STATUS_TEXT[$item['application_status']]; // 等待验证
                                $from = null;
                                $fromGroup = null;
                                $user = null;
                            }
                            break;
                        case 2:
                        case 1:
                            if ($item['receiver_id'] == $uid) {
                                $content = UserApplication::APPLICATION_STATUS_TEXT[$item['application_status']]; // 已同意
                                $from = $item['uid'];
                                $fromGroup = $item['group_id'];
                                $user = User::find($item['uid']);
                                $remark = $item['application_reason'];
                            } else {
                                $content = UserApplication::APPLICATION_STATUS_TEXT[$item['application_status']]; // 已同意
                                $from = null;
                                $fromGroup = null;
                                $user = null;
                            }
                            break;
                    }

                    break;
                case UserApplication::APPLICATION_TYPE_GROUP:
                    break;
            }

            $rtList[] = [
                'id' => $item['id'],
                'content' => $content,
                'uid' => $uid,
                'from' => $from,
                'from_group' => $fromGroup,
                'type' => $type,
                'remark' => $remark,
                'href' => null,
                'read' => $item['read_state'],
                'time' => $time,
                'user' => $user,
            ];
        }
        return $rtList;
    }

    public static function agreeFriend(int $id, int $group)
    {
        $userApply = UserApplication::find($id);
        if (! $userApply) {
            throw new ApiException(ErrorCode::USER_APPLICATION_NOT_FOUND);
        }

        if ($userApply['application_status'] != UserApplication::APPLICATION_STATUS_CREATE) {
            throw new ApiException(ErrorCode::USER_APPLICATION_PROCESSED);
        }
        // 添加好友
        FriendRelation::create([
            'uid' => $userApply['uid'],
            'friend_id' => $userApply['receiver_id'],
            'friend_group_id' => $userApply['group_id'],
        ]);
        FriendRelation::create([
            'uid' => $userApply['receiver_id'],
            'friend_id' => $userApply['uid'],
            'friend_group_id' => $group,
        ]);

        // 更改信息状态
        $rs = $userApply::where(['id' => $id])->update(['application_status' => UserApplication::APPLICATION_STATUS_ACCEPT, 'read_state' => UserApplication::ALREADY_READ]);
        return $rs;
    }

    public static function refuseFriend(int $id)
    {
        $userApply = UserApplication::find($id);
        if (! $userApply) {
            throw new ApiException(ErrorCode::USER_APPLICATION_NOT_FOUND);
        }

        if ($userApply['application_status'] != UserApplication::APPLICATION_STATUS_CREATE) {
            throw new ApiException(ErrorCode::USER_APPLICATION_PROCESSED);
        }

        // 更改信息状态
        $rs = $userApply::where(['id' => $id])->update(['application_status' => UserApplication::APPLICATION_STATUS_REFUSE, 'read_state' => UserApplication::ALREADY_READ]);
        return $rs;
    }
}
