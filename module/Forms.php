<?php

namespace zetsoft\system\module;

use zetsoft\dbitem\data\Config;
use zetsoft\dbitem\data\Form;
use zetsoft\dbitem\data\FormDb;
use zetsoft\system\actives\ZActiveQuery;
use zetsoft\system\actives\ZModel;
use zetsoft\system\Az;
use zetsoft\system\actives\ZActiveRecord;
use yii\data\ActiveDataProvider;
use zetsoft\system\kernels\ZActiveTrait;
use zetsoft\widgets\inputes\ZHInputWidget;
use zetsoft\widgets\inputes\ZKSelect2Widget;
use zetsoft\widgets\inputes\ZKStarRatingWidget;
use zetsoft\widgets\inputes\ZKSwitchInputWidget;


/**
 * This is the model class for table "ALL".
 *
 * @property int $id
 * @property string $name
 * @property string $title
 */
class Forms extends ZModel
{


    public function config()
    {
        return static function (Config $config) {
            return $config;
        };
    }


    public function column()
    {
        return [

            'name' => static function (Form $form) {
                $form->rules = [
                    ['zetsoft\\system\\validate\\ZRequiredValidator'],
                ];

                $form->title = Az::l('Название');
                $form->showForm = true;

                $form->widget = ZKStarRatingWidget::class;
                $form->options = [];

                return $form;
            },


        ];
    }


    public function card()
    {
        return [

        ];
    }
  

}
