<?php
/*
 * Copyright (c) 2023.
 * @author David Xu <david.xu.uts@163.com>
 * All rights reserved.
 */

namespace davidxu\config\helpers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use davidxu\base\enums\AppIdEnum;

/**
 * Response result helper
 *
 * Class ResponseHelper
 * @package davidxu\config\helpers
 */
class ResponseHelper
{
    /**
     * @param int $code
     * @param string $message
     * @param array $data
     * @return array|mixed
     */
    public static function json(int $code = 404, string $message = '', array $data = []): mixed
    {
        if (!$message) {
            $message = Yii::t('app', 'Unknown error');
        }
        $appId = Yii::$app->params['appId'] ?? Yii::$app->id;
        if (in_array($appId, AppIdEnum::api(), true)) {
            return static::api($code, $message, $data);
        }
        return static::baseJson($code, $message, $data);
    }

    /**
     * Return Json
     *
     * @param int $code Http status code
     * @param string $message Returned message
     * @param object|array $data Returned data array or object
     * @return array
     */
    protected static function baseJson(int $code, string $message, object|array $data): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        return [
            'code' => (string)$code,
            'message' => trim($message),
            'data' => $data ? ArrayHelper::toArray($data) : [],
        ];
    }

    /**
     * Returns array data format
     * if data is api, returns json
     *
     * @param int $code Http status code
     * @param string $message Returned message
     * @param object|array $data Returned data array or object
     * @return array|array[]|mixed|object|object[]|string|string[]
     */
    protected static function api(int $code, string $message, object|array $data): mixed
    {
        Yii::$app->response->setStatusCode($code, $message);
        Yii::$app->response->data = $data ? ArrayHelper::toArray($data) : [];

        return Yii::$app->response->data;
    }
}
