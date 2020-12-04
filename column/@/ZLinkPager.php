<?php

namespace zetsoft\system\column;

use kop\y2sp\ScrollPager;
use yii\bootstrap4\Html;
use yii\helpers\ArrayHelper;
use zetsoft\system\Az;
use yii\bootstrap4\LinkPager;
use yii\data\Pagination;
use yii\web\JsExpression;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\widgets\navigat\ZButtonWidget;



class ZLinkPagsadasder extends LinkPager
{

    

    public $pagination;

    public $options = [];

    public $listOptions = ['class' => ['pagination']];

    public $linkContainerOptions = ['class' => ['page-item']];

    public $linkOptions = ['class' => ['page-link']];

    public $pageCssClass;

    public $firstPageCssClass = 'first';

    public $lastPageCssClass = 'last';

    public $prevPageCssClass = 'prev';

    public $nextPageCssClass = 'next';

    public $activePageCssClass = 'active';

    public $disabledPageCssClass = 'disabled';

    public $disabledListItemSubTagOptions = [];

    public $maxButtonCount = 10;

    public $nextPageLabel = "<span aria-hidden=\"true\">&raquo;</span>\n<span class=\"sr-only\">{Next}</span>";

    public $prevPageLabel = "<span aria-hidden=\"true\">&laquo;</span>\n<span class=\"sr-only\">{Previous}</span>";

    public $firstPageLabel = false;

    public $lastPageLabel = false;

    public $registerLinkTags = false;

    public $hideOnSinglePage = true;

    public $disableCurrentPageButton = false;


    public $allUrl;
    public $url;


    protected function renderPageButton($label, $page, $class, $disabled, $active)
    {
        $options = $this->linkContainerOptions;
        $linkWrapTag = ArrayHelper::remove($options, 'tag', 'li');
        Html::addCssClass($options, empty($class) ? $this->pageCssClass : $class);

        $linkOptions = $this->linkOptions;
        $linkOptions['data-page'] = $page;

        if ($active) {
            Html::addCssClass($options, $this->activePageCssClass);
        }
        if ($disabled) {
            Html::addCssClass($options, $this->disabledPageCssClass);
            $disabledItemOptions = $this->disabledListItemSubTagOptions;
            $linkOptions = ArrayHelper::merge($linkOptions, $disabledItemOptions);
            $linkOptions['tabindex'] = '-1';
        }
       // $this->url = Az::$app->controller->id.Az::$app->controller->module->id.Az::$app->controller->actionId;
        //$this->allUrl = substr($this->pagination->createUrl($page), 0, strlen($this->url) - 2) . '/'.Az::$app->controller->actionId . substr($this->pagination->createUrl($page), strlen($this->url) - 2);

        return Html::tag($linkWrapTag, Html::a($label, $this->pagination->createUrl($page), $linkOptions), $options);
    }

    public function run()
    {

        //vdd($this->prevPageLabel);
        $this->prevPageLabel = strtr($this->prevPageLabel,
            [
                '{Previous}' => Az::l('Предыдущий')
            ]
        );
        $this->nextPageLabel = strtr($this->nextPageLabel,
            [
                '{Next}' => Az::l('Следущий')
            ]
        );

        return parent::run();
    }


}
