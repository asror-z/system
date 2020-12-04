<?php

namespace zetsoft\system\column;

use kop\y2sp\ScrollPager;
use zetsoft\system\Az;
use zetsoft\widgets\navigat\ZButtonWidget;


class ZScrollPagerOdilov extends ScrollPager
{

    public $linkPagerWrapperTemplate = '{pager}';
   
    public $linkContainerOptions;
    public $linkOptions;
    public $disabledListItemSubTagOptions;

    public function run()
    {
      



            $this->container = '.kv-grid-container table tbody';
            $this->item      = 'tr';
            $this->paginationSelector = '.grid-view .pagination';
            $this->triggerTemplate = '<tr class="ias-trigger"><td colspan="100% style="text-align: center"><a style="cursor: pointer">Yanaaa</a></td></tr>';
            $this->enabledExtensions  = [
                self::EXTENSION_SPINNER,
                self::EXTENSION_NONE_LEFT,
                self::EXTENSION_PAGING,
            ];


        
    

       

      
//        vdd($this);


        $js = <<<JS
            
       var tbody =   $(".kv-grid-container table tbody");
       var p =     $('.grid-view .pagination');
       console.log(p.attr('class'))     
            
JS;

        $this->view->registerJs($js);

        parent::run();
    }


}
