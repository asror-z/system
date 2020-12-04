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
use zetsoft\dbitem\data\Form;
use zetsoft\system\Az;
use zetsoft\system\control\ZCoreTrait;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZHTML;
use zetsoft\system\helpers\ZInflector;
use zetsoft\system\helpers\ZStringHelper;
use zetsoft\system\helpers\ZUrl;
use zetsoft\system\kernels\ZTrait;
use zetsoft\widgets\inputes\ZKSelect2Widget;

class ZKDataColumn extends DataColumn
{


    /**
     * @var
     * Add Data
     */
    public $editableOptions = [
        'editableValueOptions' => [
            'class' => 'text-success'
        ]
    ];

    public $defaultSort;
    public $sort;


    public $readonlyKernel;
    public $widgetClass;
    public $widgetOptions;
    /**
     * @var Form
     */
    public $column;


    /**
     * @var
     * Kernel Data
     */

    public $dbType;
    public $vAlign = 'middle';
    public $hAlign = 'center';
    public $readonly = false;


    public $header;
    public $columnKey = null;
    public $format = self::format['raw'];  //     || 'raw'  ||  'html'  || ['date' ; 'php:Y-m-d']
    public $width = '';

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
        'class' => 'font-weight-bold',
        'data-colspan-dir' => 'ltr',
    ];
    public $pageSummaryFormat = 'text';
    public $hidePageSummary = false;


    public $headerOptions = [
        'class' => 'td-dynawidget ',
    ];
    public $contentOptions = [
        'class' => 'td-dynawidget  align-items-center ',
    ];
    public $beforeHeader = [];
    public $footerOptions = [];

    public const format = [
        'text' => 'text',
        'raw' => 'raw',
        'html' => 'html',
    ];

    use ZCoreTrait;


    #region FILTER OF TABLE

    protected function renderFilterCellContent()
    {

        if (empty($this->model) || !$this->model->configsRead->customFilter)
            return parent::renderFilterCellContent();

        $filterTypes = [
            //dbTypeInteger,
            //dbTypeString,
            //dbTypeBigInteger,
            //dbTypeTime,
            //dbTypeDate,
        ];

        $column = $this->modelColumn;

        $data = $this->getData();

        if (ZArrayHelper::isIn($column->dbType, $filterTypes)) {

            $column->filterWidget = ZKSelect2Widget::class;

            $this->filterWidgetOptions = ZArrayHelper::merge([
                'config' => [
                    'multiple' => true,
                    'showToggleAll' => false,
                ],
            ], $this->filterWidgetOptions);

            $table_name = ZInflector::underscore($this->modelClassName);

            if (empty($data)) {

                Az::$app->forms->wiData->clean();
                Az::$app->forms->wiData->fkTable = $table_name;
                Az::$app->forms->wiData->isFilter = true;

                $data = Az::$app->forms->wiData->related();
                $data = $this->processData($data);

            }

        }

        $widgetClass = $column->filterWidget;

        $column->value = null;

        $options = ZArrayHelper::merge($this->filterWidgetOptions, [
            'attribute' => $this->attribute,
            'data' => $data,
            'config' => [
                'class' => 'filter-dyna-widgets'
            ],

        ]);

        return $widgetClass::widget($options);

    }

    private function getData()
    {

        Az::$app->forms->wiData->clean();
        Az::$app->forms->wiData->model = $this->model;
        Az::$app->forms->wiData->attribute = $this->attribute;

        return Az::$app->forms->wiData->data();

    }

    private function processData($data)
    {

        $return = [];

        foreach ($data as $key => $item) {
            if (!empty($item) && !empty($key))
                $return[$key] = $item;
        }

        return $return;
    }

    #endregion

    #region HEADER OF TABLE (SORT)

    private function getDefaultSort($sorts)
    {

        $return = '';
        foreach ($sorts as $key => $sort) {

            if ($sort === SORT_DESC)
                $return .= '-' . $key . ',';
            else
                $return .= $key . ',';

        }

        if (ZStringHelper::endsWith($return, ','))
            $return = str_replace(',', '', $return);

        return $return;

    }

    private function getCleanSort($sorts)
    {

        $cleanSort = [];
        foreach ($sorts as $sort) {
            $attribute = str_replace('-', '', $sort);
            $cleanSort[$attribute] = $sort;
        }

        return $cleanSort;
    }

    private function getSort($cleanSort)
    {

        $return = [];
        foreach ($cleanSort as $key => $value) {

            if ($key === $this->attribute) {

                $return[$key] = '-' . $key;
                if (ZStringHelper::startsWith($value, '-'))
                    $return[$key] = $key;

            } else
                $return[$key] = $value;

        }

        if (!ZArrayHelper::keyExists($this->attribute, $cleanSort))
            $return = ZArrayHelper::merge($cleanSort, [
                $this->attribute => $this->attribute
            ]);

        $return = ZArrayHelper::merge([
            $this->attribute => $return[$this->attribute]
        ], $return);

        return implode(',', $return);

    }

    private function getClass($cleanSort)
    {

        $class = '';
        foreach ($cleanSort as $key => $sort) {

            if ($key !== $this->attribute)
                continue;

            if (ZStringHelper::startsWith($sort, '-'))
                $class = 'desc';
            else
                $class = 'asc';

        }

        return $class;

    }

    protected function renderHeaderCellContent()
    {

        if (empty($this->model) || !$this->model->configsRead->customSort)
            return parent::renderHeaderCellContent();

        $column = $this->modelColumn;

        if (!empty($this->httpGet('sort')))
            $sorts = $this->httpGet('sort');
        else
            $sorts = $this->getDefaultSort($this->defaultSort);

        $sortsArray = [];
        if (!empty($sorts))
            $sortsArray = explode(',', $sorts);

        $cleanSort = $this->getCleanSort($sortsArray);

        $getSort = $this->getSort($cleanSort);
        $class = $this->getClass($cleanSort);

        $params = ZArrayHelper::merge($this->urlParam, [
            'sort' => $getSort,
        ]);

        $href = $this->urlArrayStr . '.aspx?' . ZUrl::makeUrl($params);

        return strtr($this->layout['sort'], [
            '{class}' => $class,
            '{attribute}' => $this->attribute,
            '{href}' => $href,
            //  '{href}' => $this->urlTo($params),
            '{label}' => $column->title,
            '{sort}' => $getSort,
        ]);
    }

    #endregion


    #region CORE


    public function isReadonly($model)
    {
        /**
         *
         * Main
         */
        $this->readonly = $this->modelColumn->readonly;

      //  vd($this->modelColumn->readonly);
        if ($this->callableCheck($this->modelColumn->readonly)) {
            $readonly = $this->modelColumn->readonly;
            $this->readonly = $readonly($model, $this);
        }

        /**
         *
         * Second
         */
        $this->readonlyKernel = $this->modelColumn->readonlyKernel;

        if ($this->callableCheck($this->modelColumn->readonlyKernel)) {
            $readonlyKernel = $this->modelColumn->readonlyKernel;
            $this->readonlyKernel = $readonlyKernel($model, $this);
        }
        if ($this->readonly !== true)
            $this->readonly = $this->readonlyKernel;

    }

    public function init()
    {
        parent::init();
        $this->trait();

        if ($this->model !== null)
            $this->modelColumn = $this->model->columns[$this->attribute];
            
        $this->headerOptions['class'] .= " editable-dyna-$this->attribute";
    }

    #endregion

}


