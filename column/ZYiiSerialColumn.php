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
use yii\grid\SerialColumn;
use yii\helpers\Html;


class ZYiiSerialColumn extends SerialColumn
{
    
    public $content;
    public $contentOptions;
    public $filterOptions;
    public $footer;
    public $footerOptions;
    public $grid;
    public $header = '#';
    public $headerOptions;
    public $options;
    public $visible;

 
}
