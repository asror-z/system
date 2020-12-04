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

class VisualsValidator extends Validator
{
    public $message = 'Hello App';

    public function validateAttribute($model, $attribute)
    {
    
        if (in_array($model->$attribute, $model->attributes)) {
            $this->addError($model, $attribute, 'This Model Classname is excist');
        }
    }

}
