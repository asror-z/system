<?php

namespace zetsoft\system\module;

use zetsoft\webs\rest\ZCloneAction;
use zetsoft\apisys\rest\ZCloneAllAction;
use zetsoft\apisys\crud\ZCreateAction;
use zetsoft\apisys\crud\ZDeleteAction;
use zetsoft\apisys\rest\ZDeleteAllAction;
use zetsoft\apisys\crud\ZEditAction;
use zetsoft\actions\crud\ZIndexAction;
use zetsoft\actions\crud\ZUpdateAction;
use zetsoft\actions\crud\ZViewAction;
use zetsoft\webs\files\ZDeleteFileAction;
use zetsoft\webs\files\ZUploadAction;
use zetsoft\models\page\PageAction;
use Yii;
use yii\helpers\ArrayHelper;
use zetsoft\system\control\ZControlWeb;


class Controls extends ZControlWeb
{

    public function init()
    {
        $this->model = Models::class;
        parent::init();
    }


}
