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


use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\ContentNegotiator;
use yii\filters\RateLimiter;
use yii\filters\RateLimitInterface;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;
use zetsoft\apisys\apps\rest;
use zetsoft\dbdata\web\ActionRestData;
use zetsoft\dbdata\web\ActionTerrabaytData;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\restact\ZCreateAction;
use zetsoft\system\restact\ZDeleteAction;
use zetsoft\system\restact\ZIndexAction;
use zetsoft\system\restact\ZOptionsAction;
use zetsoft\system\restact\ZUpdateAction;
use zetsoft\system\restact\ZViewAction;


class ZActiveControl extends Controller
{
    use ZCoreTrait;

    public $serializer = [
        'class' => 'yii\rest\Serializer',
        'collectionEnvelope' => 'data',
    ];

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
            'contentNegotiator' => [
                'class' => ContentNegotiator::className(),
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                    'application/xml' => Response::FORMAT_XML,
                ],
            ],
            'verbFilter' => [
                'class' => VerbFilter::className(),
                'actions' => $this->verbs(),
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
        $restData = (new ActionTerrabaytData())->main();
        $this->enableCsrfValidation = false;
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


        return $restData;
    }

    public function afterAction($action, $result)
    {
        $result = parent::afterAction($action, $result);
        return $this->serializeData($result);
    }

    protected function serializeData($data)
    {
        return Az::createObject($this->serializer)->serialize($data);
    }

    protected function verbs()
    {
        return [
            'index' => ['GET', 'HEAD'],
            'view' => ['GET', 'HEAD'],
            'create' => ['POST'],
            'update' => ['POST'],
            'delete' => ['DELETE'],
        ];
    }

    public function checkAccess($action, $model = null, $params = [])
    {
    }


}
