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

namespace App\Controller\Http;

use App\Middleware\JwtAuthMiddleware;
use App\Service\FriendService;
use Exception;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\RequestMapping;

#[AutoController(prefix: 'friend')]
class FriendController extends CommonController
{
    #[RequestMapping(path: 'createFriendGroup', methods: 'POST')]
    #[Middleware(JwtAuthMiddleware::class)]
    public function createFriendGroup()
    {
        $user = $this->request->getAttribute('user');
        $friendGroupName = $this->request->input('friend_group_name');
        try {
            $result = FriendService::createFriendGroup($user->id, $friendGroupName);
        } catch (Exception $e) {
            return $this->resp->error($e->getCode(), $e->getMessage());
        }

        return $this->resp->success([
            'id' => $result->id,
            'groupname' => $result->friend_group_name,
        ]);
    }

    #[RequestMapping(path: 'getRecommendedFriend', methods: 'GET')]
    #[Middleware(JwtAuthMiddleware::class)]
    public function getRecommendedFriend()
    {
        $user = $this->request->getAttribute('user');
        return $this->resp->success(FriendService::getRecommendedFriend($user['id'], 20));
    }

    #[RequestMapping(path: 'apply', methods: 'POST')]
    #[Middleware(JwtAuthMiddleware::class)]
    public function apply()
    {
        $user = $this->request->getAttribute('user');
        $receiverId = $this->request->input('receiver_id');
        $friendGroupId = $this->request->input('friend_group_id');
        $applicationReason = $this->request->input('application_reason');
        return $this->resp->success(FriendService::apply($user->id, (int) $receiverId, (int) $friendGroupId, (string) $applicationReason));
    }
}
