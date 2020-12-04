<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace zetsoft\system\column;

use yii\data\Pagination;
use zetsoft\system\Az;
use Yii;
use zetsoft\system\helpers\ZArrayHelper;


class ZPaginationOdilov extends Pagination
{

    public $route;
    public $pageSize = 20;
    public $pageParam = 'page';
    public $pageSizeParam = 'per-page';
    public $forcePageParam = true;
    public $params = null;
    public $urlManager = null;
    public $validatePage = true;
    public $totalCount = 0;
    public $pageSizeLimit = [1, 50];

    public $defaultPageSize = 20;
    public $minPageSize = 5;
    public $maxPageSize = 20;
    public $dynaGridOptions = [];

    public function init()
    {


        Az::$app->getModule('dynagrid')->defaultPageSize = $this->defaultPageSize;
        Az::$app->getModule('dynagrid')->maxPageSize = $this->maxPageSize;
        Az::$app->getModule('dynagrid')->minPageSize = $this->minPageSize;

        parent::init();


    }

}
