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
use kartik\grid\GridView;
use yii\base\Model;
use yii\helpers\Html;
use zetsoft\system\control\ZCoreTrait;
use zetsoft\system\helpers\ZHTML;
use zetsoft\system\kernels\ZTrait;

class ZDataColumn extends DataColumn
{

    public $vAlign = 'middle';
    public $hAlign = 'center';

    public $columnKey = null;
    public $format = 'raw';  //     || 'raw'  ||  'html'  || ['date' ; 'php:Y-m-d']
    public $width = null;

    public $hidden = false;
    public $noWrap = false;

    public $mergeHeader = true;
    public $hiddenFromExport = false;

    public $exportMenuStyle = [];
    public $xlFormat = null;

    public $pageSummary = false;
    public $pageSummaryFunc = GridView::F_SUM;
    public $pageSummaryOptions = [
        'prepend' => null,
        'append' => null,
        'colspan' => null,
        'data-colspan-dir' => 'ltr',
    ];
    public $pageSummaryFormat = 'text';
    public $hidePageSummary = false;


    use ZCoreTrait;

    public function init()
    {
        parent::init();
        $this->trait();
    }

    protected function renderFilterCellContent()
    {

        if (is_string($this->filter)) {
            return $this->filter;
        }

        $model = $this->grid->filterModel;

        if ($this->filter !== false && $model instanceof Model && $this->attribute !== null && $model->isAttributeActive($this->attribute)) {
            if ($model->hasErrors($this->attribute)) {
                Html::addCssClass($this->filterOptions, 'has-error');
                $error = ' ' . Html::error($model, $this->attribute, $this->grid->filterErrorOptions);
            } else {
                $error = '';
            }
            if (is_array($this->filter)) {
                $options = array_merge(['prompt' => ''], $this->filterInputOptions);
                return Html::activeDropDownList($model, $this->attribute, $this->filter, $options) . $error;
            } elseif ($this->format === 'boolean') {
                $options = array_merge(['prompt' => ''], $this->filterInputOptions);
                return Html::activeDropDownList($model, $this->attribute, [
                        1 => $this->grid->formatter->booleanFormat[1],
                        0 => $this->grid->formatter->booleanFormat[0],
                    ], $options) . $error;
            }

            return Html::activeTextInput($model, $this->attribute, $this->filterInputOptions) . $error;
        }

        $content = parent::renderFilterCellContent();
        $chkType = !empty($this->filterType) && $this->filterType !== GridView::FILTER_CHECKBOX &&
            $this->filterType !== GridView::FILTER_RADIO && !class_exists($this->filterType);
        if ($this->filter === false || empty($this->filterType) || $content === $this->grid->emptyCell || $chkType) {
            return $content;
        }
        $widgetClass = $this->filterType;
        $options = [
            'model' => $this->grid->filterModel,
            'attribute' => $this->attribute,
            'options' => $this->filterInputOptions,
        ];
        if (is_array($this->filter)) {
            if (Config::isInputWidget($this->filterType) && $this->grid->pjax) {
                $options['pjaxContainerId'] = $this->grid->getPjaxContainerId();
            }
            if ($this->filterType === GridView::FILTER_SELECT2 || $this->filterType === GridView::FILTER_TYPEAHEAD) {
                $options['data'] = $this->filter;
            }
            if ($this->filterType === GridView::FILTER_RADIO) {
                return ZHTML::activeRadioList(
                    $this->grid->filterModel,
                    $this->attribute,
                    $this->filter,
                    $this->filterInputOptions
                );
            }
        }
        if ($this->filterType === GridView::FILTER_CHECKBOX) {
            return ZHTML::activeCheckbox($this->grid->filterModel, $this->attribute, $this->filterInputOptions);
        }
        $options = array_replace_recursive($this->filterWidgetOptions, $options);
        /** @var \kartik\base\Widget $widgetClass */


        return $widgetClass::widget($options);
    }


}
