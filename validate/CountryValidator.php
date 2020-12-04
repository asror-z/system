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

class CountryValidator extends Validator
{
    public $message = 'Hello App';

    public function validateAttribute($model, $attribute)
    {
        if (!in_array($model->$attribute, ['USA', 'Indonesia'])) {
            $this->addError($model, $attribute, 'The country must be either "{country1}" or "{country2}".', ['country1' => 'USA', 'country2' => 'Indonesia']);
        }
    }


    public function clientValidateAttribute($model, $attribute, $view)
    {

        if ($model->$attribute === 'Asror')
            $message = json_encode('AAA', JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        else
            $message = json_encode('BBB', JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        return <<<JS
    messages.push($message);
JS;

    }
}
