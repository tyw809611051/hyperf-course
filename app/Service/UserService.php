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

namespace App\Service;

use App\Model\User;


class UserService
{
    public static function findUserByEmail(string $email)
    {
        return User::query()->where('email','=',$email)->first() ?? NULL;
    }

    /**
     * @param string $email
     * @param string $password
     *
     * @return User
     * */
    public static function login(string $email, string $password) :User
    {

       return self::findUserByEmail($email);
//        if (!$user || $user['delete_at'] !== NULL) {
//
//        }
    }
}