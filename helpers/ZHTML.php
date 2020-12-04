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


use kartik\helpers\Html;
use zetsoft\system\Az;

class ZHTML extends Html
{
    public static function a($text, $url = null, $options = [])
    {
        if (PHP_SAPI !== 'cli')
            return parent::a($text, $url, $options);
        else
            return 'A Tag';
    }



    /**
     * use:
     * <?= \common\helpers\Html::phone('+7 (999) 00-00-000', ['class' => 'phone']); ?>
     *
     * result:
     * <a class="phone" href="tel:+7 (999) 00-00-000">+7 (999) 00-00-000</a>
     *
     * Class Html
     * @package common\helpers
     */


    /**
     * @param $phone
     * @param array $options
     * @return string
     */
    public static function phone($phone, $options = [])
    {
        $options['href'] = 'tel:' . $phone;
        if (!isset($options['class'])) {
            $options['class'] = '';
        }
        return static::tag('a', $phone, $options);
    }


    public static function getInputName($model, $attribute)
    {
        $return = parent::getInputName($model, $attribute);
        $return = Az::$app->utility->pregs->pregReplace($return, '(\w*)\[\[(\d+)\](\w+)\]\[(\w+)\]', '$1[$2][$3][$4]');

        return $return;
    }


    public static function getAttributeValue($model, $attribute)
    {

        $value = parent::getAttributeValue($model, $attribute);
        return $value;

    }


}
