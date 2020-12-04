<?php


namespace zetsoft\system\validate;


use zetsoft\dbcore\ALL\UserCore;
use zetsoft\former\auth\AuthLoginForm;
use zetsoft\system\actives\ZValidator;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZInflector;

/**
 * Class    ZSiteNameValidator
 * @package zetsoft\system\validate
 * @author Daho
 * @since 17.08.2020
 */
class ZSiteNameValidator extends ZValidator
{
    public function init()
    {
        parent::init();
        $this->message = Az::l('Это имя нельзя использовать в качестве названия сайта.');
    }

    public function validateAttribute($model, $attribute)
    {
        $value = $model->$attribute;
        $latin = preg_match('/^[A-Za-z0-9\s_-]+$/u', $value);
        $startWith = !ZInflector::startWithNumber($value);
        $probel = strpos($value, ' ') === false;
        if ($latin && $startWith && $probel)
            return true;

        $this->addError($model, $attribute, $this->message);
        return false;


    }

    public function clientValidateAttribute($model, $attribute, $view)
    {

        
        $errorMessage = Az::l('Это имя нельзя использовать в качестве названия сайта.');

        return <<<JS
       
       if (allLetter(this.value))
            return true;
       else
            message.push('$errorMessage');
       
       function allLetter(inputtxt) {
            var letters = ^[A-Za-z]+$;
            if (!inputtxt.value.match(letters)) {
                //message.push('$errorMessage');
                return false;
            }
            else
                return true;
            
        }
JS;

    }

}
