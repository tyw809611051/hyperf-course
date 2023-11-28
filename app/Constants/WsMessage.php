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

namespace App\Constants;

use Hyperf\Constants\AbstractConstants;
use Hyperf\Constants\Annotation\Constants;

/**
 * Class WsMessage.
 * @Constants
 */
class WsMessage extends AbstractConstants
{
    public const WS_MESSAGE_CMD_EVENT = 'system.event';

    public const WS_MESSAGE_CMD_ERROR = 'system.error';

    public const EVENT_USER_STATUS = 'setUserStatus';

    public const EVENT_GET_MESSAGE = 'getMessage';

    public const EVENT_GET_UNREAD_APPLICATION_COUNT = 'getUnreadApplicationCount';

    public const EVENT_FRIEND_AGREE_APPLY = 'friendAgreeApply';

    public const EVENT_GROUP_AGREE_APPLY = 'groupAgreeApply';

    public const EVENT_FRIEND_VIDEO_ROOM = 'friendVideoRoom';
}
