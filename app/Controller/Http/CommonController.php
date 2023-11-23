<?php
declare(strict_types=1);

/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf-cloud/hyperf/blob/master/LICENSE
 */

namespace App\Controller\Http;

use App\Component\Response;
use App\Controller\AbstractController;
use Hyperf\Di\Annotation\Inject;

class CommonController extends AbstractController
{

    #[Inject]
    protected Response $resp;
}