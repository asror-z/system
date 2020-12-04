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

namespace zetsoft\system\actives;


use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;

class ZArrayData extends ArrayDataProvider
{

    use ZProviderTrait;

    public $behaviors;
    public $count;
    public $db;
    public $id;
    public $key;
    public $keys;
    public $models;
    //public $pagination;
    public $query;
    public $sort;
    public $columns;
    public $totalCount;

    protected function prepareModels()
    {
        return parent::prepareModels();
    }

    protected function prepareKeys($models)
    {
        return parent::prepareKeys($models);
    }

    protected function prepareTotalCount()
    {
        return parent::prepareTotalCount();
    }

    protected function sortModels($models, $sort)
    {
        return parent::sortModels($models, $sort);
    }
}
