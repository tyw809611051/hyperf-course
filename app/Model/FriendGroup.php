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
 * @property string $friend_group_name
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property string $deleted_at
 */
class FriendGroup extends Model
{
    /**
     * The table associated with the model.
     */
    protected ?string $table = 'friend_group';

    /**
     * The attributes that are mass assignable.
     */
    protected array $fillable = ['id', 'uid', 'friend_group_name', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * The attributes that should be cast to native types.
     */
    protected array $casts = ['id' => 'integer', 'uid' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];
}
