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


use yii\data\Sort;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\widgets\LinkSorter;
use zetsoft\system\helpers\ZHTML;
use zetsoft\system\kernels\ZWidget;

class ZSorter extends LinkSorter
{
    public $options = [];
    public $linkOptions = [];


    protected function renderSortLinks()
    {
        $attributes = empty($this->attributes) ? array_keys($this->sort->attributes) : $this->attributes;
        $links = '';
        $icon = '';
        $all = '';
        $icons = ['fas fa-chevron-up'];
        foreach ($attributes as $name) {
            $links .= ZHTML::tag('div', $this->sort->link($name, $this->linkOptions), $this->options);
            $icon .= ZHTML::tag('i', '', $icons);

            $all = $icon;
            $all .= $links;
        }
        
        return $all;
       
    }
}
