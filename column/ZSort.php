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

class ZSort extends Sort
{
    public $enableMultiSort = true;
    public $sortParam = 'sort';
    public $defaultOrder = SORT_DESC;
    public $separator = ',';
    public $params = null;
    public $urlManager = null;
    public $sortFlags = SORT_REGULAR;
    public $route;

}
