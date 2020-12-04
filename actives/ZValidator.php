<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * Date:    9/12/2019
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\system\actives;


use yii\validators\InlineValidator;
use yii\validators\Validator;
use zetsoft\system\Az;
use zetsoft\system\control\ZCoreTrait;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\kernels\ZTrait;

class ZValidator extends Validator
{


    use ZCoreTrait;

    public function init()
    {
        parent::init();
        $this->trait();
    }

    public static function createValidator($type, $model, $attributes, $params = [])
    {

        if (is_array($type)) {
            $type = ZArrayHelper::getValue($type, 0);
        }

        $params['attributes'] = $attributes;

        switch (true){
            case $type instanceof \Closure:
                $params['class'] = InlineValidator::class;
                $params['method'] = $type;
                break;
            case ZArrayHelper::keyExists($type, static::$builtInValidators) && $model->hasMethod($type):
                $params['class'] = InlineValidator::class;
                $params['method'] = [$model, $type];
                break;
            default:
                unset($params['current']);
                if (ZArrayHelper::keyExists($type, static::$builtInValidators))
                    $type = static::$builtInValidators[$type];
                if (is_array($type))
                    $params = array_merge($type, $params);
                else
                    $params['class'] = $type;

        }

        return Az::createObject($params);

    }

    public static function createValidatorOld($type, $model, $attributes, $params = [])
    {

        if (is_array($type)) {
            // vd($type);
            $type = ZArrayHelper::getValue($type, 0);
        }

        return parent::createValidator($type, $model, $attributes, $params);

    }


}
