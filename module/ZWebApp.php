<?php

/**
 *
 * Author:  Shakhrizod Nurmukhammadov
 *
 */

namespace zetsoft\system\module;


use yii\base\Controller;
use yii\base\InvalidRouteException;
use yii\web\Application;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use zetsoft\dbitem\core\RestItem;
use zetsoft\dbitem\core\WebItem;
use zetsoft\service\ALL\ALL;
use zetsoft\service\cores\Rbac;
use zetsoft\system\Az;
use zetsoft\system\control\ZControlWeb;
use zetsoft\system\control\ZCoreTrait;
use zetsoft\system\except\ZException;
use zetsoft\system\except\ZForbiddenException;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZInflector;
use zetsoft\system\kernels\ZModule;
use zetsoft\system\kernels\ZView;
use zetsoft\widgets\former\ZGrapesJsWidgetMaladoy;
use zetsoft\widgets\former\ZGrapesJsWidgetRav;
use zetsoft\widgets\former\ZGrapesJsWidgetRavshan;


/**
 * Class    ZWebApp
 * @package zetsoft\system\module
 *
 * @mixin ALL
 * @mixin app
 *
 * @property ZView $view
 * @property ZControlWeb $controller
 */
class ZWebApp extends Application
{
    use ZCoreTrait;

    public function init()
    {
        parent::init();
        $this->trait();
    }

    public function run()
    {
        return parent::run();
    }

    public function handleRequest($request)
    {
        return parent::handleRequest($request);
    }

    public function coreComponents()
    {
        return [
            'log' => ['class' => 'yii\log\Dispatcher'],
            'i18n' => ['class' => 'yii\i18n\I18N'],
            'security' => ['class' => 'yii\base\Security'],
            'request' => ['class' => 'yii\web\Request'],
            'response' => ['class' => 'yii\web\Response'],
        ];
    }

    public function beforeAction($action)
    {
        $this->behaviors();
        return parent::beforeAction($action);
    }

    /**
     *
     * Function  runAction
     * @param string $route
     * @param array $params
     * @return  mixed|string|null
     * @throws InvalidRouteException
     * @throws ZException
     * @throws \yii\base\InvalidConfigException
     *
     *
     */

    public $result;

    public function lastKey()
    {

        if (count($this->urlArray) === 0)
            return null;

        $lastKey = array_key_last($this->urlArray);
        $lastVal = $this->urlArray[$lastKey];

        return $lastVal;
    }


    public function crudAllow()
    {

        if (ZArrayHelper::isIn($this->lastKey(), self::exclude))
            $return = true;
        else
            $return = false;

        return $return;
    }

    public function runAction($route, $params = [])
    {

        if (ZArrayHelper::keyExists($this->moduleId, $this->modules)) {
            $this->result = parent::runAction($route, $params);
            return $this->result;
        }

        $this->cors();


        /*  @var $controller Controller */
        $this->controller = new ZControlWeb($route, null);
        $this->controller->module = new ZModule(App);

        $this->controller->enableCsrfValidation = false;

        switch ($this->moduleId) {

            /**
             *
             * API
             */

            case 'api':

                $this->paramSet(paramRest, true);
                Az::$app->response->format = Response::FORMAT_JSON;
                $url = $this->modifyUrl(0);
                $mainFile = Root . "/webrest$url.php";
                $this->result = $this->renderApi($mainFile, 'api');
                break;


            case 'rest':
                $this->paramSet(paramRest, true);
                // /rest/{modelName}/{action}.aspx
                Az::$app->response->format = Response::FORMAT_JSON;
                $mainFile = Root . '/webrest/core/crud/' . $this->actionId . '.php';
                $model = $this->urlData(1);
                $this->paramSet('jsonModel', $this->bootFull(ZInflector::camelize($model)));

                $this->result = $this->renderApi($mainFile, 'rest');
                break;

            /**
             *
             * Grapes
             */

            case 'edit':
                $this->result = $this->renderGrapes();
                break;


            /**
             *
             * Web Crud
             */

            case 'crud':
                $mainFile = "/webhtm/core/crud/{$this->actionId}.php";

                Az::$app->cores->rbac->target = Rbac::target['crud'];
                if (Az::$app->cores->rbac->run())
                    $this->result = $this->renderWeb($mainFile);
                else
                    $this->forbidden();

                break;


            case 'read':
                Az::$app->cores->rbac->target = Rbac::target['crud'];
                $mainFile = "/webhtm/core/read/{$this->actionId}.php";
                if (!Az::$app->cores->rbac->run())
                    $this->forbidden();
                    
                $this->result = $this->renderWeb($mainFile);

                break;


            /**
             *
             * Default Web
             */

            default:
                $this->result = $this->renderWebHtm($this->urlArrayStr);
                break;
        }

        $this->result();

        return $this->result;

    }


    public function forbidden() {
        global $boot;
        if ($boot->env('loginRedirect'))
            $this->urlRedirect($boot->env('urlLogin'));
        else
            throw new ZForbiddenException();

    }

    public function result()
    {

        // $this->result = Az::$app->parser->htmlCompress->minify($this->result);

        //$this->result = Az::$app->parser->Phpwee->minify($this->result);

        // vdd( $this->result );


        //$this->result = Az::$app
        //$this->result = Az::$app

    }

    private function renderApi($mainFile, $typeApi = 'api')
    {
        if (!file_exists($mainFile))
            throw new NotFoundHttpException();
//            return $this->requireApi($mainFile, ZArrayHelper::merge($this->httpPost() , $this->httpGet()));

        $file = require $mainFile;

        /** @var RestItem $action */

        $action = $this->paramGet(paramAction);

        if (!($action)) {
            $this->restItem();
            $action = $this->paramGet(paramAction);
        }

        switch ($action->authType) {
            case Rbac::authType['param']:
                Az::$app->cores->authRest->param();
                break;

            case Rbac::authType['bearer']:
                Az::$app->cores->authRest->bearer();
                break;

            case Rbac::authType['basic']:
                Az::$app->cores->authRest->basic();
                break;

            case Rbac::authType['basicParam']:
                Az::$app->cores->authRest->basicParam();
                break;
        }

//        if (!Az::$app->cores->rbac->run(Rbac::target[$typeApi]))
//            throw new ZForbiddenException();

        return $file;

    }

    private function renderGrapes()
    {
        $url = $this->modifyUrl(0);
        if (Az::$app->cores->rbac->getRole() !== 'admin') {
            $newFile = $this->modifyUrl(0, true) . "/App/{$this->actionId}_" . App;
            $file = 'webhtm' . $newFile . '.php';
            if (file_exists($file))
                $url = $this->modifyUrl(0, true) . $newFile;
            // For RBAC
            //            else
            //                throw new NotFoundHttpException();
        }

        $code = $this->require('/webhtm/core/grape/index.php', [
            'url' => $url
        ]);

        return $code;
    }

    public const exclude = [
        'fast-register-frame',
        'fast-register',
        'register',
        'register-frame',
        'logout',
        'logout-frame',
        'login',
        'login-frame',
    ];


    private function renderWebHtm($defaultUrl)
    {
        global $boot;

        if (Az::$app->utility->urlApp->isMain()) {
            $defaultUrl = str_replace('.aspx', '', Az::$app->homeUrl);
            $mainFile = "/webhtm{$defaultUrl}.php";
        } else {
            $mainFile = "/webhtm{$defaultUrl}.php";
        }
        Az::$app->cores->rbac->target = Rbac::target['view'];
        
        if ($this->crudAllow() || Az::$app->cores->rbac->run())
            return $this->renderWeb($mainFile);

        if ($this->userIsGuest())
            return $this->urlRedirect($boot->env('urlLogin') . '?returnUrl=' . $this->urlMain);

        $this->forbidden();
    }

    private function renderWeb($mainFile)
    {
        Az::$app->cores->session->setCookieSession();
        if (!file_exists(Root . $mainFile))
            throw new NotFoundHttpException($this->urlPath);

        $this->require($mainFile);

        /** @var WebItem $action */

        $action = $this->paramGet(paramAction);

        if (!($action)) {
            $this->webItem();
            $action = $this->paramGet(paramAction);
        }
        if ($action !== null)
            if (!$action->csrf)
                $this->controller->enableCsrfValidation = false;

        switch ($action->type) {

            case 'ajax':
                return $this->requireAjax($mainFile);
                break;

            case 'part':
                return $this->requirePart($mainFile);
                break;

            case 'partFile':
                return $this->requirePartFile($mainFile);
                break;

            case 'ajaxFile':
                return $this->requireAjaxFile($mainFile);
                break;

            case 'ajaxFilePreg':
                return $this->requireAjaxFilePreg($mainFile);
                break;

            default:
                if ($action->layout) {
                    return $this->require($this->layout($action), [
                        'content' => $mainFile
                    ]);
                }

                return $this->require($mainFile);
                break;
        }
    }

    private function layout(WebItem $action)
    {
        $data = '/webhtm/lays/' . App . '/' . $action->layoutFile . '.php';

        return $data;
    }

    private function webItem()
    {
        $action = new WebItem();

        $action->title = Azl . 'Тест';
        $action->icon = 'fal fa-lastfm-square';
        $action->type = WebItem::type['html'];
        $action->csrf = true;
        $action->debug = true;

        $this->paramSet(paramAction, $action);

        $this->title();
        $this->toolbar();
    }

    private function restItem()
    {
        global $boot;
        $item = new RestItem();

        $item->authType = $boot->env('defaultRestAuth');
        $item->cache = false;
        $item->cacheHttp = false;

        $this->paramSet(paramAction, $item);
    }

    private function cors()
    {
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            /*header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");*/
            header("Access-Control-Allow-Origin: *");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');    // cache for 1 day
        }
// Access-Control headers are received during OPTIONS requests
        if (ZArrayHelper::getValue($_SERVER, 'REQUEST_METHOD') === 'OPTIONS') {

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                /*header("Access-Control-Allow-Methods: GET, POST, OPTIONS, POST, PUT");*/
                header("Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE, PUT, PATCH");

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

            exit(0);
        }
    }
}
