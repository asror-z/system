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


class ZPaginationOld extends  Pagination
{
    use ZCoreTrait;

    /**
     * @return array
     * @property Widget $widget
     */

    public function init()
    {
        parent::init();
        $this->trait();
    }

    public $pageParam = 'page';
    public $forcePageParam = true;
    public $pageSizeParam = 'per-page';
    public $route = true;
    public $params = [];
    public $urlManager;


    public function getLinks($absolute = false)
    {
        $currentPage = $this->getPage();
        $pageCount = $this->getPageCount();
        $links = [
            Link::REL_SELF => $this->createUrl($currentPage, null, $absolute),
        ];
        if ($currentPage > 0) {
            $links[self::LINK_FIRST] = $this->createUrl(0, null, $absolute);
            $links[self::LINK_PREV] = $this->createUrl($currentPage - 1, null, $absolute);
        }
        if ($currentPage < $pageCount - 1) {
            $links[self::LINK_NEXT] = $this->createUrl($currentPage + 1, null, $absolute);
            $links[self::LINK_LAST] = $this->createUrl($pageCount - 1, null, $absolute);
        }

        return $links;
    }
}
