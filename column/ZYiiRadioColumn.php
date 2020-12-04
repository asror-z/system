<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\system\column;


use yii\base\InvalidConfigException;
use yii\grid\RadioButtonColumn;
use yii\helpers\Html;


class ZYiiRadioColumn extends RadioButtonColumn
{
    public $name = 'radioButtonSelection';
    public $radioOptions = [];
    public $content;
    public $contentOptions;
    public $filterOptions;
    public $footer;
    public $footerOptions;
    public $grid;
    public $header;
    public $headerOptions;
    public $options;
    public $visible;

 
}
