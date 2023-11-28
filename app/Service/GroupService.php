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
use App\Model\Group;
use App\Model\GroupRelation;

class GroupService
{
    public static function findGroupById(int $groupId)
    {
        $groupInfo = Group::query()->where(['id' => $groupId])->first();
        if (! $groupInfo) {
            throw new ApiException(ErrorCode::GROUP_NOT_FOUND);
        }
        return $groupInfo;
    }

    public static function createGroup(int $userId, string $groupName, string $avatar, int $size, string $introduction, int $validation)
    {
        $groupId = Group::query()->insertGetId([
            'uid' => $userId,
            'group_name' => $groupName,
            'avatar' => $avatar,
            'size' => $size,
            'introduction' => $introduction,
            'validation' => $validation,
        ]);
        if (! $groupId) {
            throw new ApiException(ErrorCode::GROUP_CREATE_FAIL);
        }
        $groupRelationId = GroupRelation::query()->insertGetId([
            'uid' => $userId,
            'group_id' => $groupId,
        ]);
        if (! $groupRelationId) {
            throw new ApiException(ErrorCode::GROUP_RELATION_CREATE_FAIL);
        }
        return self::findGroupById($groupId);
    }

    public static function getRecommendedGroup(int $limit)
    {
        return Group::query()->whereNull('deleted_at')->orderBy('created_at', 'desc')->limit($limit)->get()->toArray();
    }
}
