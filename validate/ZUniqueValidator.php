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

class ZUniqueValidator extends ZValidator
{


    public function init()
    {
        parent::init();
        $this->message = Az::l('Данное имя пользователя уже занято');
    }


    /**
     *
     * Function  validateAttribute
     * @param AuthLoginForm $model
     * @param string $attribute
     * @return bool
     */
    public function validateAttribute($model, $attribute)
    {
        global $boot;

        $user = Az::$app->cores->auth->user($model->name);

        if ($user === null)
        {
            $this->addError($model, $attribute, $this->message);
            return false;
        }

        if ($boot->env('passHashed'))
            $state = Az::$app->security->validatePassword($model->password, $user->password);
        else
            $state = $user->password === $model->password;

        if ($state)
            return true;

        $this->addError($model, $attribute, $this->message);

        return false;
    }


}
