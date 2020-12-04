<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 **/

namespace zetsoft\system\column;


use Yii;
use yii\data\Pagination;
use yii\web\Link;
use yii\web\Linkable;
use yii\web\Request;
use zetsoft\service\smart\Widget;
use zetsoft\system\Az;
use zetsoft\system\control\ZCoreTrait;
use zetsoft\system\helpers\ZUrl;


class ZPagination extends Pagination
{
    use ZCoreTrait;

    /**
     * @return array
     * @property Widget $widget
     */

    public $pageSize;
    public $pageSizeLimit;
    public $totalCount;

    public $pageParam = 'page';
    public $forcePageParam = false;
    public $pageSizeParam = 'per-page';

    public $route = true;
    public $params;
    public $validatePage = true;


    public function init()
    {
        /*        $this->pageSizeLimit = [
                    $this->minPageSize,
                    $this->maxPageSize
                ];*/

        parent::init();
        $this->trait();
    }


    public function createUrl($page, $pageSize = null, $absolute = false)
    {
        $page = (int)$page;
        $pageSize = (int)$pageSize;

        if (($params = $this->params) === null) {
            $request = Yii::$app->getRequest();
            $params = $request instanceof Request ? $request->getQueryParams() : [];
        }
        if ($page > 0 || $page === 0 && $this->forcePageParam) {
            $params[$this->pageParam] = $page + 1;
        } else {
            unset($params[$this->pageParam]);
        }
        if ($pageSize <= 0) {
            $pageSize = $this->getPageSize();
        }
        if ($pageSize !== $this->defaultPageSize) {
            $params[$this->pageSizeParam] = $pageSize;
        } else {
            unset($params[$this->pageSizeParam]);
        }

      
        $params[0] = $this->route === null ? $this->urlArrayStr : $this->route;
        $urlManager = $this->urlManager === null ? Yii::$app->getUrlManager() : $this->urlManager;

        if ($absolute) {
            return $urlManager->createAbsoluteUrl($params);
        }

        return $urlManager->createUrl($params);
    }
}
