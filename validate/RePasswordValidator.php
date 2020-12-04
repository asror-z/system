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
 * Author: Jahongir Qudratov
 */

namespace zetsoft\system\validate;

use zetsoft\system\actives\ZValidator;
use zetsoft\system\Az;

class RePasswordValidator extends ZValidator
{

    public function init()
    {
        parent::init();
        $this->message = Az::l('Пароли должны быть одинаковыми');
    }

    public function validateAttribute($model, $attribute)
    {
        $first_password = $model->password;
        $value = $model->$attribute;
        if ($first_password === $value){
            return true;
        }
        else{
            $this->addError($model, $attribute, $this->message);
            return false;
        }


    }

    public function clientValidateAttribute($model, $attribute, $view)
    {



        $errorMessage = Az::l('Пароли должны быть одинаковыми');

        return <<<JS
        var pass = $('[name="AuthPasswordRestoreForm[password]"]').val();
        var re_pass = $('[name="AuthPasswordRestoreForm[re_password]"]').val(); 
        
        if (re_pass !== pass)
            messages.push('$errorMessage');
        else
            return true;
JS;

    }

}
