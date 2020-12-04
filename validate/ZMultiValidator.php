<?php

use yii\validators\Validator;

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

use zetsoft\system\actives\ZValidator;

class ZMultiValidator extends ZValidator
{
    
    public function validateAttribute($model, $attribute)
    {
        if (!in_array($model->$attribute, $model->attribute)) {
            $this->addError($model, $attribute, "Max widgets 3");
        }
    }

}
