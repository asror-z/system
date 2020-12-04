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


use PHPUnit\Util\Log\JSON;
use zetsoft\system\Az;
use zetsoft\system\control\ZCoreTrait;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZHTML;
use zetsoft\system\helpers\ZStringHelper;
use zetsoft\system\helpers\ZVarDumper;
use zetsoft\system\kernels\ZTrait;
use zetsoft\widgets\former\ZEditableIframeWidget;
use zetsoft\widgets\former\ZEditableIframeWidgetAs;
use zetsoft\widgets\navigat\ZButtonWidget;

class ZEditableSweetColumnAsSort extends ZKDataColumn
{

    public $editableOptions = [
        'editableValueOptions' => [
            'class' => 'text-success'
        ]
    ];

    public $defaultSort;
    public $sort;

    public $layout = [

        'editable' => <<<HTML

<div class="rv-editable-link {class}">
   {value} 
</div>
HTML,

        'sort' => <<<HTML

<a class="{class}" href="{href}" data-sort="{sort}">{label}</a>
HTML,

    ];


    public $readonly = false;
    public $widgetClass;
    public $widgetOptions;

    public function renderDataCellContent($model, $keys, $index)
    {

        // return $model[$this->attribute];

        $key = ZArrayHelper::getValue($model, 'id');

        $id = $this->model->className . '-' . $this->attribute . '-' . $key;

        //  $value = '';

        Az::$app->forms->wiData->clean();
        Az::$app->forms->wiData->model = $this->model;
        Az::$app->forms->wiData->attributes = $model;
        Az::$app->forms->wiData->attribute = $this->attribute;

        $value = Az::$app->forms->wiData->value();

        $column = $this->model->columns[$this->attribute];

        $options = [
            'title' => $column->title,
            'widgetClass' => $this->widgetClass,
            'index' => $index,
            'key' => $key,
            'attribute' => $this->attribute,
            'width' => '500px',
            'height' => '300px',
        ];

        $readonly = '';
        if ($this->readonly)
            $readonly = 'readonly';

        $btn = ZHTML::tag('span', $value, [
            'id' => 'editable-' . $id,
            'aria-label' => $column->tooltip,
            'class' => "hint--top point-raw dyna-editable $readonly",
            'widget-options' => \yii\helpers\Json::htmlEncode($this->widgetOptions),
            'options' => \yii\helpers\Json::htmlEncode($options),
            "data-{$this->attribute}" => ZVarDumper::value($value),
        ]);

        return strtr($this->layout['editable'], [
            '{value}' => $btn,
            '{class}' => "editable-dyna-$this->attribute",
        ]);

        /*
                return ZEditableIframeWidgetAs::widget([
                    'model' => $model,
                    'attribute' => $this->attribute,
                    'widgetClass' => $this->widgetClass,
                    'widgetOptions' => $this->widgetOptions,
                    //  'readonly' => $this->readonly,
                    //   'id' => $id,
                    'config' => [
                        'formId' => 'edit-form',
                        'class' => 'editable-dyna-' . $this->attribute,
                        'modalId' => 'ravshan',
                        'width' => '500px',
                        'height' => '300px',
                        'key' => $key,
                        'index' => $index,
                    ]
                ]);*/

    }

    public function getDefaultSort($sorts)
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

    private function getCleanSort($sorts) {

        $cleanSort = [];
        foreach ($sorts as $sort) {
            $attribute = str_replace('-', '', $sort);
            $cleanSort[$attribute] = $sort;
        }

        return $cleanSort;
    }

    public function getSort($cleanSort)
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

        return implode(',', $return);

    }

    private function getClass($cleanSort) {

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
        $column = $this->model->columns[$this->attribute];

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

        return strtr($this->layout['sort'], [
            '{class}' => $class,
            '{href}' => $this->urlArrayStr . '.aspx?sort=' . $getSort,
            '{label}' => $column->title,
            '{sort}' => $getSort,
        ]);
    }


}

