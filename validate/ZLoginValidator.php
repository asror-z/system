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

class ZLoginValidator extends ZValidator
{


    public function init()
    {
        parent::init();
        $this->message = Az::l('Имя пользователя или пароль введен неправильно');
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
        /**
         * https://www.amcharts.com/docs/v4/reference/mappolygon/#tooltipX_property
         */
        if ($this->isEmailCheck($model->login) || $this->isTelNumber($model->login)) {

            $user = Az::$app->cores->auth->userCheck($model->login);

            if ($user === null) {
                $this->addError($model, $attribute, $this->message);
                return false;
            }
            if ($boot->env('passHashed'))
                $state = Az::$app->cores->auth->hashCheck($model->password, $user->password);
            else
                $state = $user->password === $model->password;

            if ($state)
                return true;
        }

        $this->addError($model, $attribute, $this->message);
        return false;
    }

    private function isTelNumber(&$value)
    {
        $value = str_replace(array('+', '-', ' '), '', $value);
        return is_numeric($value);
    }

    private function isEmailCheck(&$value)
    {
        if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
            return true;
        }
        return false;
    }

}
