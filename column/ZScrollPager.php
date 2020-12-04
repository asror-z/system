<?php

namespace zetsoft\system\column;

use kop\y2sp\ScrollPager;
use zetsoft\system\Az;
use zetsoft\widgets\navigat\ZButtonWidget;


class ZScrollPager extends ScrollPager
{

    public $isGrid = true;
    public $container;
    public $options=[];
    public $item;
    public $paginationSelector = '.grid-view .pagination';
    public $next = '.next a';
    public $delay = 600;
    public $negativeMargin = 10;
    public $triggerTemplate = '<tr class="ias-trigger"><td colspan="100%" style="text-align: left"><a style="cursor: pointer">{text}</a></td></tr>';
    public $triggerText = 'Показать еще...';
    public $triggerOffset = 0;
    public $spinnerSrc;
    public $spinnerTemplate = '<div class="ias-spinner" style="text-align: center;"><img src="{src}"/></div>';
    public $noneLeftText = '';
    public $noneLeftTemplate = '<div class="ias-noneleft" style="text-align: center;">{text}</div>';
    public $historyPrev = '.previous';
    public $overflowContainer;

    public $enabledExtensions = [
        self::EXTENSION_TRIGGER,
        self::EXTENSION_SPINNER,
        self::EXTENSION_NONE_LEFT,
        self::EXTENSION_PAGING,
        self::EXTENSION_HISTORY
    ];


    public $eventOnScroll;
    public $eventOnLoad;
    public $eventOnLoaded;
    public $eventOnRender;
    public $eventOnRendered;
    public $eventOnNoneLeft;
    public $eventOnNext;
    public $eventOnPageChange;
    public $eventOnReady;

    public $firstPageCssClass = 'first';
    public $lastPageCssClass = 'last';
    public $prevPageCssClass = 'prev';
    public $nextPageCssClass = 'next';
    public $activePageCssClass = 'active';
    public $pageCssClass;

    public $pagination;
    public $linkPager = [];
    public $linkPagerOptions;


    public $linkPagerWrapperTemplate = '{pager}';
   
    public $linkContainerOptions;
    public $linkOptions;
    public $disabledListItemSubTagOptions;

    public function run()
    {
        if ($this->isGrid) {
        
            $this->container = '.grid-view tbody';
            $this->item      = 'tr';
            $this->paginationSelector = '.grid-view .pagination';
            $this->triggerTemplate = '<tr class="ias-trigger"><td colspan="100% style="text-align: center"><a style="cursor: pointer">{text}</a></td></tr>';

        }
        $button = ZButtonWidget::widget([
            'config' => [
                'btnStyle' => ZButtonWidget::btnStyle['btn-outline-primary'],
                'text' => Az::l('Показать еще'),
                'btnSize' => 'btn-md',
                'btnType' => ZButtonWidget::btnType['button'],
            ]
        ]);


        $this->triggerText = $button;


        parent::run();
    }


}
