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
use yii\grid\CheckboxColumn;
use yii\helpers\Html;
use yii\helpers\Json;

class ZYiiCheckboxColumn extends CheckboxColumn
{
    public $name = 'selection';
    public $checkboxOptions = [];
    public $multiple = true;
    public $cssClass;
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
