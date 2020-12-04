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


use yii\filters\auth\HttpBasicAuth;
use yii\web\Controller;
use yii\web\Response;
use zetsoft\apisys\apps\rest;
use zetsoft\dbdata\web\ActionRestData;
use zetsoft\dbitem\core\WebItem;
use zetsoft\dbitem\core\RestItem;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;


class ZControlRest extends Controller
{

    use ZCoreTrait;

    public $authType = self::authType['none'];

    public const authType = [
        'none' => 'none',
        'param' => 'param',
        'bearer' => 'bearer',
        'basic' => 'basic',
        'basicParam' => 'basicParam',
    ];

    public function behaviors()
    {
        return [
            'corsFilter' => [
                'class' => \yii\filters\Cors::className(),
                'cors' => [
                    'Origin' => ['*'],
                    'Access-Control-Request-Headers' => ['*'],
                ],
            ],
        ];
    }

    public function init()
    {
        parent::init();
        $this->trait();
        $this->enableCsrfValidation = false;

        if (!$this->isCLI())
            Az::$app->response->format = Response::FORMAT_JSON;
    }

    public function actions()
    {
        $mainFile = $this->viewPath . "/{$this->actionId}.php";

        $this->paramSet('mainFile', $mainFile);

        //   $this->require($mainFile);

        /** @var RestItem $action */
        $action = $this->paramGet(paramAction);

        $webData = (new ActionRestData())->main();

        $this->enableCsrfValidation = false;

        if (!ZArrayHelper::keyExists($this->actionId, $webData)) {
            switch ($this->authType) {
                case 'param':
                    Az::$app->cores->authRest->param();
                    break;
                case 'bearer':
                    Az::$app->cores->authRest->bearer();
                    break;
                case 'basic':
                    Az::$app->cores->authRest->basic();
                    break;
                case 'basicParam':
                    Az::$app->cores->authRest->basicParam();
                    break;
            }
        }

        if (!is_file($mainFile))
            return $webData;


        $mainApp[$this->actionId] = [
            'class' => rest::class,
        ];

        return ZArrayHelper::merge($webData, $mainApp);
    }
}
