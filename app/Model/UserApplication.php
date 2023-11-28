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
 * @property int $uid
 * @property int $receiver_id
 * @property int $group_id
 * @property string $application_type
 * @property int $application_status
 * @property string $application_reason
 * @property int $read_state
 * @property string $deleted_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class UserApplication extends Model
{
    public const APPLICATION_STATUS_CREATE = 0;

    public const APPLICATION_STATUS_ACCEPT = 1;

    public const APPLICATION_STATUS_REFUSE = 2;

    public const UN_READ = 0;

    public const ALREADY_READ = 1;

    public const APPLICATION_STATUS_TEXT = ['等待验证', '已同意', '已拒绝'];

    public const APPLICATION_CREATE_USER = 'create';

    public const APPLICATION_RECEIVER_USER = 'receiver';

    public const APPLICATION_SYSTEM = 'system';

    public const APPLICATION_TYPE_FRIEND = 'friend';

    public const APPLICATION_TYPE_GROUP = 'group';

    /**
     * The table associated with the model.
     */
    protected ?string $table = 'user_application';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'uid', 'receiver_id', 'group_id', 'application_type', 'application_status', 'application_reason', 'read_state', 'deleted_at', 'created_at', 'updated_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'uid' => 'integer', 'receiver_id' => 'integer', 'group_id' => 'integer', 'application_status' => 'integer', 'read_state' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
