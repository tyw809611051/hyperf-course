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
use App\Model\FriendGroup;

class FriendService
{

    public static function findFriendGroupById(int $friendGroupId)
    {
        $result = FriendGroup::query()->where(['id' => $friendGroupId])->first();
        if (!$result) {
            throw new ApiException(ErrorCode::FRIEND_GROUP_NOT_FOUND);
        }
        return $result;
    }

    public static function createFriendGroup(int $uid, string $friendGroupName)
    {
        $friendGroupId = FriendGroup::query()->insertGetId([
            'uid' => $uid,
            'friend_group_name' => $friendGroupName,
        ]);
        if (! $friendGroupId) {
            throw new ApiException(ErrorCode::FRIEND_GROUP_CREATE_FAIL);
        }
        return self::findFriendGroupById($friendGroupId);
    }
}
