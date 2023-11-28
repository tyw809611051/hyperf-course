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

use App\Exception\ApiException;
use Hyperf\HttpServer\Annotation\AutoController;
use Throwable;

#[AutoController(prefix: 'util')]
class UtilController extends CommonController
{
    #[RequestMapping(path: 'uploadImg', methods: 'POST')]
    public function uploadImg()
    {
        try {
            $file = $this->request->file('file');
            if (! $file) {
                throw new ApiException(400, 'FILE_DOES_NOT_EXIST');
            }
            $size = $file->getSize();
            if ($size / 1024 / 1024 > 10) {
                throw new ApiException(400, '文件大小超过10M');
            }
            $extName = $file->getExtension();

            $dir = BASE_PATH . '/public/storage/upload/';
            $dirName = date('Ymd') . '/';
            $dir = $dir . $dirName;
            if (! is_dir($dir)) {
                @mkdir($dir, 0777, true);
            }

            $fileName = time() . rand(1, 999999);
            $path = $dir . $fileName . '.' . $extName;

            $file->moveTo($path);
            return $this->resp->success([
                'src' => env('STORAGE_IMG_URL') . $dirName . $fileName . '.' . $extName,
            ]);
        } catch (Throwable $throwable) {
            return $this->resp->error($throwable->getCode(), $throwable->getMessage());
        }
    }

    #[RequestMapping(path: 'uploadFile', methods: 'POST')]
    public function uploadFile()
    {
        try {
            $file = $this->request->file('file');
            if (! $file) {
                throw new ApiException(400, 'FILE_DOES_NOT_EXIST');
            }
            $size = $file->getSize();
            if ($size / 1024 / 1024 > 10) {
                throw new ApiException(400, '文件大小超过10M');
            }
            $extName = $file->getExtension();

            $dir = BASE_PATH . '/public/file/upload/';
            $dirName = date('Ymd') . '/';
            $dir = $dir . $dirName;
            if (! is_dir($dir)) {
                @mkdir($dir, 0777, true);
            }

            $fileName = time() . rand(1, 999999);
            $path = $dir . $fileName . '.' . $extName;

            $file->moveTo($path);
            return $this->resp->success([
                'src' => env('STORAGE_FILE_URL') . $dirName . $fileName . '.' . $extName,
                'name' => $fileName . '.' . $extName,
            ]);
        } catch (Throwable $throwable) {
            return $this->resp->error($throwable->getCode(), $throwable->getMessage());
        }
    }
}
