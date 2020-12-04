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

namespace zetsoft\system\column;


use kartik\base\Config;
use kartik\grid\DataColumn;
use kartik\grid\ExpandRowColumn;
use kartik\grid\GridView;
use yii\base\Model;
use yii\helpers\Html;
use yii\helpers\Url;
use zetsoft\system\control\ZCoreTrait;
use zetsoft\system\helpers\ZHTML;
use zetsoft\system\kernels\ZTrait;

class ZExpandRowColumn extends ExpandRowColumn
{

    public $detailRowCssClass = GridView::TYPE_DEFAULT;
    public $detailAnimationDuration = 'fast';

    public $enableCache = false;
    public $enableRowClick = false;
    public $rowClickExcludedTags = [
        'a',
        'button',
        'input'
    ];
    public $extraData = [];

    public $defaultHeaderState = GridView::ROW_COLLAPSED;
    public $expandOneOnly = true;
    public $allowBatchToggle = true;
    public $disabled = false;
    public $detailOptions = [];
    public $expandicon = '';
    public $collapseicon = '';
    public $expandTitle = '';
    public $collapseTitle = '';
    public $expandAllTitle = '';
    public $collapseAllTitle = '';
    public $onDetailLoaded = '';

    public $headerOptions = [];
    public $contentOptions = [];
    public $beforeHeader = [];
    public $footerOptions = [];

    

    use ZCoreTrait;

    public function init()
    {
        parent::init();
        $this->trait();
    }


}


