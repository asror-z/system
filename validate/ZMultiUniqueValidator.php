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

namespace zetsoft\system\validate;


use yii\validators\Validator;
use zetsoft\system\Az;
use function Dash\Curry\find;

class ZMultiUniqueValidator extends Validator
{
    public function init()
    {
        parent::init();
        $this->message = Az::l('Вы можете сохранить только уникальные значения.');
    }

    public function validateAttribute($model, $attribute)
    {
        $arr = $model->$attribute;

        foreach ($arr as $key => $value){
            foreach ($arr as $k => $v){
                if ($k !== $key && $value === $v){
                    $this->addError($model, $attribute, $this->message);
                    return false;
                }
            }
        }

        return true;


    }


 
}
