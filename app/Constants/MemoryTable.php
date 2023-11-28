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
 * Class MemoryTable.
 * @Constants
 */
class MemoryTable extends AbstractConstants
{
    public const FD_TO_USER = 'fdToUser';

    public const USER_TO_FD = 'userToFd';

    public const SUBJECT_USER_TO_FD = 'subjectUserToFd';

    public const SUBJECT_FD_TO_USER = 'subjectFdToUser';

    public const SUBJECT_TO_USER = 'subjectToUser';

    public const USER_TO_SUBJECT = 'userToSubject';
}
