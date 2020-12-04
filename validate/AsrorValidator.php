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

class AsrorValidator extends ZValidator
{
    
    public function validateAttribute($model, $attribute)
    {
        if (!in_array($model->$attribute, ['USA', 'Indonesia'])) {
            $this->addError($model, $attribute, 'The country must be either "{country1}" or "{country2}".', ['country1' => 'USA', 'country2' => 'Indonesia']);
        }
    }

}
