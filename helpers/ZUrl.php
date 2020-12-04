<?php

/**
 *
 *
 * Author:  Asror Zakirov
 *
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\system\helpers;


use yii\helpers\Url;
use zetsoft\system\Az;
use zetsoft\system\control\ZCoreTrait;
use zetsoft\system\kernels\ZView;
use function DusanKasan\Knapsack\splitWith;
use function zetsoft\apisys\edit\returnn;

class ZUrl extends Url
{

    public static function makeUrl($params, $scheme = false)
    {
        return http_build_query($params);
    }

    public static function to($url = null, $scheme = false)
    {

        if (PHP_SAPI !== 'cli') {
            return parent::to($url, $scheme);
        } else
            return 'cmd';
    }

    public static function urlNormalize($url)
    {

        if (ZArrayHelper::getValue($url, 0) === '/')
            return $url;

        if (ZStringHelper::startsWith($url, 'http'))
            return $url;

        $t = new ZView();
        return $t->prelastUrl() . '/' . $url;
    }


}
