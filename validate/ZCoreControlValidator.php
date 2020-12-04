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


use zetsoft\dbcore\ALL\UserCore;
use zetsoft\former\auth\AuthLoginForm;
use zetsoft\system\actives\ZValidator;
use zetsoft\system\Az;

class ZCoreControlValidator extends ZValidator
{
    public function init()
    {
        parent::init();
        $this->message = Az::l('Есть недопустимые символы');
    }

    public function validateAttribute($model, $attribute)
    {
        $arr = $model->$attribute;

        if (preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z][aA-zZ0-9\-_]+$/', $arr,))

            return true;

        $this->addError($model, $attribute, $this->message);
        return false;


    }

}
