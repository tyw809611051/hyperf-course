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
use App\Model\FriendRelation;
use App\Model\Group;
use App\Model\GroupRelation;
use App\Model\User;

class FriendService
{
    public static function findFriendGroupById(int $friendGroupId)
    {
        $result = FriendGroup::query()->where(['id' => $friendGroupId])->first();
        if (! $result) {
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

    public static function getFriendGroupByUserId(int $uid): array
    {
        return FriendGroup::query()->where(['uid' => $uid])->whereNull('deleted_at')->get()->toArray();
    }

    public static function getFriendRelationByFriendGroupIds(array $friendGroupIds): array
    {
        return FriendRelation::query()->whereNull('deleted_at')->whereIn('friend_group_id', $friendGroupIds)->get()->toArray();
    }

    public static function getFriend(int $uid)
    {
        $friendGroups = self::getFriendGroupByUserId($uid);
        $friendGroupIds = array_column($friendGroups, 'id');

        $friendRelations = self::getFriendRelationByFriendGroupIds($friendGroupIds);
        $friendRelationIds = array_column($friendRelations, 'friend_id');

        $users = UserService::getUserByIds($friendRelationIds);
        $userInfos = array_column($users, null, 'id');

        $friend = [];
        foreach ($friendGroups as $friend_group) {
            $friend[$friend_group['id']] = [
                'id' => $friend_group['id'],
                'groupname' => $friend_group['friend_group_name'],
                'list' => [],
            ];
        }

        foreach ($friendRelations as $friend_relation) {
            $userInfo = $userInfos[$friend_relation['friend_id']];
            $friend[$friend_relation['friend_group_id']]['list'][] = [
                'username' => $userInfo['username'],
                'id' => $userInfo['id'],
                'avatar' => $userInfo['avatar'],
                'sign' => $userInfo['sign'],
                'status' => FriendRelation::STATUS_TEXT[$userInfo['status']],
            ];
        }
        return array_values($friend);
    }

    public static function getGroupRelationByUserId(int $uid): array
    {
        return GroupRelation::query()->whereNull('deleted_at')->where(['uid' => $uid])->get()->toArray();
    }

    public static function getGroupByIds(array $groupIds): array
    {
        return Group::query()->whereNull('deleted_at')->whereIn('id', $groupIds)->get()->toArray();
    }

    public static function getGroup(int $uid)
    {
        $groupRelations = self::getGroupRelationByUserId($uid);
        $groupIds = array_column($groupRelations, 'group_id');

        $groupInfos = self::getGroupByIds($groupIds);
        $result = [];

        foreach ($groupInfos as $groupInfo) {
            $result[] = [
                'groupname' => $groupInfo['group_name'],
                'id' => $groupInfo['id'],
                'avatar' => $groupInfo['avatar'],
            ];
        }
        return $result;
    }

    public static function getRecommendedFriend(int $limit)
    {
        return User::query()->whereNull('deleted_at')->orderBy('created_at', 'desc')->limit($limit)->get();
    }
}
