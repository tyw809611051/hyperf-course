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
namespace App\Controller\Ws;

use App\Component\WsProtocol;
use App\Constants\MemoryTable;
use App\Controller\AbstractController;
use App\Model\UserApplication;
use App\Service\FriendService;
use App\Service\UserService;
use App\Task\FriendTask;
use Hyperf\Context\Context;
use Hyperf\HttpServer\Annotation\AutoController;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\Memory\TableManager;


#[AutoController(prefix: "friend",server: "ws")]
class FriendController extends AbstractController
{
    #[RequestMapping(path: "sendMessage",methods: "GET")]
    public function sendMessage()
    {
        /**
         * @var WsProtocol $protocol
         */
        $protocol = Context::get('request');
        $data = $protocol->getData();

        $friendChatHistoryInfo = FriendService::createFriendChatHistory($data['message_id'], $data['from_user_id'], $data['to_id'], $data['content']);
        $userInfo = UserService::findUserInfoById($data['from_user_id']);

        $fd = TableManager::get(MemoryTable::USER_TO_FD)->get($data['to_id'],'fd') ?? '';
        $this->container->get(FriendTask::class)->sendMessage(
            $fd,
            $userInfo->username,
            $userInfo->avatar,
            $data['from_user_id'],
            UserApplication::APPLICATION_TYPE_FRIEND,
            $data['content'],
            $data['message_id'],
            false,
            $data['from_user_id'],
            $friendChatHistoryInfo->created_at->getTimestamp() * 1000
        );

        return ['message_id' => $data['message_id'] ?? ''];
    }
}