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

namespace App\Model;

use Hyperf\DbConnection\Model\Model;

/**
 * @property int $id
 * @property string $message_id
 * @property int $from_uid
 * @property int $to_uid
 * @property string $content
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $deleted_at
 * @property int $reception_state
 */
class FriendChatHistory extends Model
{
    public const NOT_RECEIVED = 0;

    public const RECEIVED = 1;

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'friend_chat_history';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'message_id', 'from_uid', 'to_uid', 'content', 'created_at', 'updated_at', 'deleted_at', 'reception_state'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'from_uid' => 'integer', 'to_uid' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime', 'reception_state' => 'integer'];
}
