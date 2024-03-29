<?php
/*
 * Copyright (c) 2023.
 * @author David Xu <david.xu.uts@163.com>
 * All rights reserved.
 */

namespace davidxu\config\helpers;

use Exception;
use Ramsey\Uuid\Uuid;
use yii\helpers\StringHelper as YiiStringHelper;

/**
 * Class StringHelper
 * @package davidxu\config\helpers
 */
class StringHelper extends YiiStringHelper
{
    /**
     * Generate a random string
     *
     * @param int $length
     * @param bool $numeric
     * @return string
     * @throws Exception
     */
    public static function random(int $length, bool $numeric = false): string
    {
        $seed = base_convert(md5(microtime() . $_SERVER['DOCUMENT_ROOT']), 16, $numeric ? 10 : 35);
        $seed = $numeric ? (str_replace('0', '', $seed) . '012340567890') : ($seed . 'zZ' . strtoupper($seed));

        $hash = '';
        if (!$numeric) {
            $hash = chr(random_int(1, 26) + random_int(0, 1) * 32 + 64);
            $length--;
        }

        $max = strlen($seed) - 1;
        $seed = str_split($seed);
        for ($i = 0; $i < $length; $i++) {
            $hash .= $seed[random_int(0, $max)];
        }

        return $hash;
    }

    /**
     * Generate a number random string
     *
     * @param bool $prefix 判断是否需求前缀
     * @param int $length 长度
     * @return string
     */
    public static function randomNum(bool $prefix = false, int $length = 8): string
    {
        $str = $prefix ?? '';
        return $str . substr(implode(null, array_map('ord', str_split(substr(uniqid('', true), 7, 13)))), 0, $length);
    }
    
    /**
     * @param string $type
     * @param string $name
     * @return string
     * @throws Exception
     */
    protected static function uuid(string $type = '', string $name = 'php.net'): string
    {
        $uuid = match ($type) {
            'time' => Uuid::uuid1(),
            'md5' => Uuid::uuid3(Uuid::NAMESPACE_DNS, $name),
            'random' => Uuid::uuid4(),
            'sha1' => Uuid::uuid5(Uuid::NAMESPACE_DNS, $name),
            default => md5(uniqid(md5(microtime(true) . random_bytes(8)), true)),
        };
//        switch ($type) {
//            // represents a version 1 UUID
//            case 'time' :
//                $uuid = Uuid::uuid1();
//                break;
//            // Returns a version 3 (name-based) UUID based on the MD5 hash of a namespace ID and a name
//            case 'md5' :
//                $uuid = Uuid::uuid3(Uuid::NAMESPACE_DNS, $name);
//                break;
//            // Returns a version 4 (random) UUID
//            case 'random' :
//                $uuid = Uuid::uuid4();
//
//                break;
//            // Returns a version 5 (name-based) UUID based on the SHA-1 hash of a namespace ID and a name
//            case 'sha1' :
//                $uuid = Uuid::uuid5(Uuid::NAMESPACE_DNS, $name);
//                break;
//            // php uuid
//            default :
//                $uuid = md5(uniqid(md5(microtime(true) . random_bytes(8)), true));
//        }
        return is_string($uuid) ? $uuid : $uuid->toString();
    }

    /**
     * Parase Enum attributes
     *
     * Input format a:des1,b:des2
     *
     * @param string $string
     * @return array
     */
    public static function parseEnumAttr(string $string): array
    {
        $array = preg_split('/[,;\r\n]+/', trim($string, ",;\r\n"));
        if (strpos($string, ':')) {
            $value = [];
            foreach ($array as $val) {
                list($k, $v) = explode(':', $val);
                $value[$k] = $v;
            }
        } else {
            $value = $array;
        }

        return $value;
    }
}
