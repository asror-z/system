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
use yii\grid\ActionColumn;
use yii\grid\DataColumn;
use yii\grid\RadioButtonColumn;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Inflector;


class ZYiiActionColumn extends ActionColumn
{
    public $buttonOptions= [];
    public $buttons= [];
    public $content;
    public $contentOptions;
    public $controller;
    public $filter;
    public $filterOptions;
    public $footer;
    public $footerOptions;
    public $grid;
    public $header;
    public $headerOptions = ['class' => 'action-column'];
    public $value;
    public $options;
    public $visible;
    public $visibleButtons= [];
    public $template = '{view} {update} {delete}';
    public $urlCreator;



    
}
