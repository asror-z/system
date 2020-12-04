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


use kartik\grid\ActionColumn;
use kartik\grid\GridView;

class ZKActionColumn extends ActionColumn
{

    public $hidden = false;
    public $hiddenFromExport = false;
    public $dropdown = false;
    public $dropdownOptions = ['class' => 'float-right'];
    public $dropdownMenu = ['class'=>'text-left'];
    public $dropdownButton = ['class'=>'btn btn-secondary'];
    public $buttonOptions = [];
    public $viewOptions = [];
    public $updateOptions = [];
    public $deleteOptions = [
        'data-method' => 'post'
    ];
    public $buttons = [];
    public $hAlign = GridView::ALIGN_CENTER;
    public $vAlign = GridView::ALIGN_MIDDLE;
    public $noWrap = false;
    public $width = '80px';
    public $pageSummary = false;
    public $pageSummaryOptions = [];
    public $pageSummaryFormat = 'format';
    public $hidePageSummary = false;
    public $mergeHeader = true;




}
