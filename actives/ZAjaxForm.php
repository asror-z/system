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

namespace zetsoft\system\actives;

use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\JqueryAsset;
use zetsoft\system\control\ZCoreTrait;
use zetsoft\system\helpers\ZJsonHelper;
use zetsoft\system\kernels\ZTrait;
use zetsoft\system\kernels\ZView;


class ZAjaxForm extends ZActiveForm
{
    /**
     * @var bool If enable the ajax submit
     */
    public $enableAjaxSubmit = true;
    
    /**
     * @var array The options passed to jquery.form, Please see the jquery.form document
     */
    public $ajaxSubmitOptions = [];

    public $formAction = '#';

    public $ajaxData;
    public $layout;


    public $event = [
        'beforeSend' => "function( jqXHR , settings) {
              console.log('ZActiveForm | beforesend');
          }",
        'success' => "function(data , textStatus , jqXHR) {
              console.log('ZActiveForm | succes')
              alert()
          }",
        'error' => "function( jqXHR,  textStatus,  errorThrown) { 
            // if error occured
              console.log('ZActiveForm | error');
          }",
        'complete' => "function(jqXHR, textStatus) {
              console.log('ZActiveForm | complete');
          }",
        'done' => "function(yourVariable) {
            // do smth when you get yourVariable
            // return smthVariable
            console.log('ZActiveForm | done')
        }",
        'fail' => "function(yourVariable) {
           // do smth when you get yourVariable
             console.log('ZActiveForm | fail')
        }",
        'always' => "function(yourVariable) {
            // do smth when you get yourVariable
            console.log('ZActiveForm | always')
        }"
    ];


    public function run()
    {
        if ($this->enableAjaxSubmit) {

            $id = $this->options['id'];

            $this->view->registerJs("jQuery('#$id').yiiActiveForm()
            .on('beforeSubmit', function(_event) { 
            
            jQuery(_event.target)
            .ajaxSubmit({
                url: '{$this->formAction}',
                data: '{$this->ajaxData}',
                success: {$this->event['success']},
                complete: {$this->event['complete']},
                error: {$this->event['error']},
            }); 
            
            return false;
            
            });");
        }

        return parent::run();
    }


}
