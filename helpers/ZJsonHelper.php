<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * Date:    9/24/2019
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\system\helpers;


use yii\helpers\Json;
use zetsoft\system\Az;

class ZJsonHelper extends Json
{


    public static function encode($value, $options = JSON_PRETTY_PRINT)
    {

        if (is_string($value))
            $value = html_entity_decode($value);

        /**
         *
         * JSON_PRETTY_PRINT
         */
        if (!empty($value))
            return parent::encode($value, $options);

        return null;
    }

    public static function decode($json, $asArray = true)
    {
        $return = null;

        if (!empty($json))
            try {
                $return = parent::decode($json, $asArray);
            } catch (\Exception $e) {
                Az::warning($e->getMessage(), 'Exception in Json Decode');
            }


        return $return;
    }


}
