<?php
/**
 *
 * Author:  Asror Zakirov
 * Created: 29.06.2017 19:06
 * https://www.linkedin.com/in/asror-zakirov-167961a9
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 */

namespace zetsoft\system\control;


use zetsoft\system\Az;
use zetsoft\system\helpers\ZInflector;
use zetsoft\system\kernels\ZTrait;
use yii\console\Controller;
use function Safe\define;


class ZControlCmd extends Controller
{

    use ZCoreTrait;


    /**
     * @var bool
     *
     * Wheather all apps from applist will be applied to particular action
     */
    public $allApp = true;


    /**
     * @var array|null
     *
     * List of all Apps from Env file
     */
    public ?array $appList = [];


    public $cmd;
    
    public $app;
    public $file;
    public $dbase;
    public $table;
    public $class;
    public $folder;


    public function beforeAction($action)
    {

        switch (true) {
            case !empty($this->class):
                $this->table = ZInflector::underscore($this->class);
                break;

            case !empty($this->table):
                $this->class = ZInflector::camelize($this->table);
                break;
        }


        /**
         *
         * Apps
         */

        $this->allApp = $this->bootEnv('allApp');
        $this->appList = $this->bootEnv('appList');


        /**
         *
         * Smarts
         */

        if (!empty($this->class))
            $this->paramSet('smartClass', explode('|', $this->class));

        if (!empty($this->dbase))
            $this->paramSet('smartDbase', explode('|', $this->dbase));

        if (!empty($this->table))
            $this->paramSet('smartTable', explode('|', $this->table));

        if (!empty($this->file))
            $this->paramSet('smartFile', explode('|', $this->file));

        if (!empty($this->folder))
            $this->paramSet('smartFolder', explode('|', $this->folder));

        if (!empty($this->app))
            $this->allApp = false;

        $this->getCmd();

        return parent::beforeAction($action);
    }

    public function getCmd()
    {
        $action = $this->action->id;
        $control = $this->id;
        $module = $this->module->id;

        $this->cmd = "$module/$control/$action";
    }


    public function execute($app) {

        global $cmdlineAll;
        $this->execPhpDefault("{$this->cmd} {$cmdlineAll}", $app);
    }


    public function options($actionID)
    {
        return array_merge(parent::options($actionID), [
            'table', 'class', 'file', 'migra', 'app', 'cat', 'dbase', 'folder'
        ]);
    }

    /**
     *
     * Function init
     */
    public function init()
    {
        parent::init();
        $this->trait();
    }

}
