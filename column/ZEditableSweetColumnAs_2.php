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


use DebugBar\DataFormatter\VarDumper\DebugBarHtmlDumper;
use PHPUnit\Util\Log\JSON;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZHTML;
use zetsoft\system\helpers\ZInflector;
use zetsoft\system\helpers\ZStringHelper;
use zetsoft\system\helpers\ZUrl;
use zetsoft\system\helpers\ZVarDumper;
use zetsoft\widgets\inputes\ZKSelect2Widget;

class ZEditableSweetColumnAs extends ZKDataColumn
{


    
    public $layout = [
        'editable' => <<<HTML
<div class="rv-editable-link {class}" aria-label="{aria-label}">
   {value} 
</div>
HTML,

    ];






    #region MAIN DATA OF TABLE

    private function isRelated()
    {

        $b1 = !ZStringHelper::endsWith($this->attribute, '_id');
        $b2 = !ZStringHelper::endsWith($this->attribute, '_ids');
        $b3 = empty($this->modelColumn->fkTable);

        if ($b1 && $b2 && $b3)
            return false;

        return true;

    }

    public function getSize($column, $type = 'width', $grapes = null)
    {

        $prop = 'modal' . ucfirst($type);
        $return = $column->$prop;

        $static = $column->widget::$grapes;

        if (ZArrayHelper::keyExists($prop, $static))
            $return = ZArrayHelper::getValue($static, $prop);

        return $return;
    }



    public function renderDataCellContent($model, $keys, $index)
    {
        $key = ZArrayHelper::getValue($model, 'id');
        $column = $this->modelColumn;
        
        $this->isReadonly($model);

       // vd($column->sort);

        $id = $this->model->className . '-' . $this->attribute . '-' . $key;

        Az::$app->forms->wiData->clean();
        Az::$app->forms->wiData->model = $this->model;
        Az::$app->forms->wiData->modelAttrs = $model;
        Az::$app->forms->wiData->attribute = $this->attribute;

        $value = Az::$app->forms->wiData->value();

        $width = $this->getSize($column);
        $height = $this->getSize($column, 'height');

        $options = [
            'title' => $column->title,
            'url' => ZUrl::to([
                '/crud/'. ZInflector::dash($this->modelClassName) . '/editor',
                'modelClassName' => $this->model->className,
                'key' => $key,
                'index' => $index,
                'attribute' => $this->attribute,
                'url' => $this->urlPath . '?' . $this->urlParamStr,
            ]),
            'width' => $width,
            'height' => $height,
        ];

        vdd($options);


        if ($this->readonly)
            $readonly = 'readonly';
        else
            $readonly = 'enable-edit';

        return strtr($this->layout['editable'], [

            '{value}' => ZHTML::tag('span', $value, [
                'id' => 'editable-' . $id,
                'aria-label' => $column->tooltip,
                'class' => "point-raw dyna-editable $readonly",
                'options' => json_encode($options),
                "data-{$this->attribute}" => ZVarDumper::beauty($value),
            ]),

            '{class}' => "editable-dyna-$this->attribute hint--top",
            '{aria-label}' => $column->tooltip
        ]);

    }

    #endregion



}
