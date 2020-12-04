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
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\db\ActiveQueryInterface;
use yii\grid\DataColumn;
use yii\grid\RadioButtonColumn;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Inflector;


class ZYiiDataColumn extends DataColumn
{
    public $attribute;
    public $content;
    public $contentOptions;
    public $encodeLabel = true;
    public $enableSorting = true;
    public $filter;
    public $filterOptions;
    public $filterInputOptions;
    public $footer;
    public $footerOptions;
    public $format = 'text';
    public $grid;
    public $header;
    public $headerOptions;
    public $label;
    public $sortLinkOptions = [];
    public $value;
    public $options;
    public $visible;
   


    
}
