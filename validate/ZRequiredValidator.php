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

class ZRequiredValidator extends ZValidator
{
    public function init()
    {
        parent::init();
        $this->message = Az::l('Необходимо заполнить поля');
    }

    public function validateAttribute($model, $attribute)
    {
        $value = $model->$attribute;
        if ($this->emptyOrNullable($value)) {
            $model->addError($attribute, $this->message);
        }
    }

    public function clientValidateAttribute($model, $attribute, $view)
    {
        $message = json_encode($this->message, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $title = $model->attributeLabels()[$attribute];
        return <<<JS
console.log(value);
if (value === '' || value === undefined) {
    messages.push($message + ' ' + "$title");
}
JS;
    }
}
