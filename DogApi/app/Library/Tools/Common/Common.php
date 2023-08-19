<?php

namespace App\Library\Tools\Common;

use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

class Common
{
    /**
     * 加密算法
     * @param $pwd
     * @return string
     */
    public static function md($pwd)
    {
        $secret = config('config.password_secret');
        return md5(md5($pwd . $secret) . $secret);
    }

    /**
     * 返回uuid
     * @return string
     * @throws \Exception
     */
    public static function getUuid()
    {
        $uuid = Uuid::uuid1();
        return $uuid->getHex();
    }

    /**
     * 获取零点时间戳
     *
     * @param $time
     * @return false|int
     */
    public static function getIntTime($time)
    {
        return strtotime(date("Y-m-d", $time));
    }

    public static function intValTime($time)
    {
        return intval($time / 1000);
    }
}