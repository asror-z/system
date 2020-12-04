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

class ZActiveData extends ActiveDataProvider
{

    use ZProviderTrait;

    public $behaviors;
    public $count;
    public $db;
    public $id;
    public $key;
    public $keys;
    public $models;

    public $query;
    //public $sort;

    public $totalCount;


/*
    protected function prepareModels()
    {
        return parent::prepareModels();
        
    }*/


}
