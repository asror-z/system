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

class BalanceValidator extends ZValidator
{

    public function init()
    {
        parent::init();
        $this->message = Az::l('На счету недостаточно денег');
    }

    public function validateAttribute($model, $attribute)
    {
        $value = $model->$attribute;
        $id = $this->userIdentity()->id;
        $user = \zetsoft\models\user\User::findOne($id);
        $balance = $user->balance;
        if($value > $balance)
        {
            $this->addError($model, $attribute, $this->message);
            return false;
        }
        else
            return true;


    }

    public function clientValidateAttribute($model, $attribute, $view)
    {

        $id = $this->userIdentity()->id;
        $user = \zetsoft\models\user\User::findOne($id);
        $balance = $user->balance;
        $errorMessage = Az::l('На счету недостаточно денег');

        return <<<JS
        if (this.value > $balance)
            messages.push('$errorMessage');
        else
            return true;
JS;

    }

}
