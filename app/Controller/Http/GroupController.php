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
use App\Service\GroupService;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Throwable;

#[AutoController(prefix: 'group')]
class GroupController extends CommonController
{
    #[RequestMapping(path: 'create', methods: 'POST')]
    #[Middleware(JwtAuthMiddleware::class)]
    public function create()
    {
        $user = $this->request->getAttribute('user');
        $groupName = $this->request->input('group_name');
        $avatar = $this->request->input('avatar');
        $size = $this->request->input('size');
        $introduction = $this->request->input('introduction');
        $validation = $this->request->input('validation');
        return $this->resp
            ->success(GroupService::createGroup($user->id, $groupName, $avatar, (int) $size, $introduction, (int) $validation));
    }

    #[RequestMapping(path: 'getRecommendedGroup', methods: 'GET')]
    #[Middleware(JwtAuthMiddleware::class)]
    public function getRecommendedGroup()
    {
        try {
            return $this->resp->success(GroupService::getRecommendedGroup(20));
        } catch (Throwable $throwable) {
            return $this->resp->error($throwable->getCode(), $throwable->getMessage());
        }
    }

    #[RequestMapping(path: 'getGroupRelation', methods: 'GET')]
    #[Middleware(JwtAuthMiddleware::class)]
    public function getGroupRelation()
    {
        $groupId = $this->request->input('id');
        return $this->resp->success(GroupService::getGroupRelationById((int) $groupId));
    }
}
