<?php
/**
 * 描述
 * Created on 2023/11/22 11:30 下午
 * Create by tangyw@sqqmall.com
 */
declare(strict_types = 1);

namespace App\Helper;

if (function_exists('jsonSuccessV2')) {
    function jsonSuccessV2($data = [], $code = 0, $msg = 'success')
    {
        $result = [
            'data'   => $data,
            'code' => $code,
            'msg'    => $msg,
        ];

        return $result;
    }
}