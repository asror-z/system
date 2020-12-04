<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\system\helpers;


use kartik\icons\icon;

class Zicon extends icon
{

    public static function show($name, $options = [])
    {
        if (empty($options))
            $options = [
                'class' => 'fas fa-shopping-basket',
                'framework' => icon::FA
            ];

        return parent::show($name, $options);
    }

    public static function fa($icon)
    {
        return "fa fa-$icon";
    }

    public static function fas($icon)
    {
        return "fas fa-$icon";
    }

    public static function far($icon)
    {
        return "far fa-$icon";
    }

}
