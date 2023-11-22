<?php
declare (strict_types=1);
namespace App\Model;


/**
 * @property int $id
 * @property int $uid
 * @property int $user_login_ip
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 */
class UserLoginLog extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected ?string $table = 'user_login_log';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected array $fillable = ['id', 'uid', 'user_login_ip', 'created_at', 'updated_at'];
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected array $casts = ['id' => 'integer', 'uid' => 'integer', 'user_login_ip' => 'integer', 'created_at' => 'datetime', 'updated_at' => 'datetime'];

}