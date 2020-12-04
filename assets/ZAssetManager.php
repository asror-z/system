<?php


namespace zetsoft\system\assets;

use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;
use yii\helpers\FileHelper;
use yii\helpers\Url;
use yii\web\AssetBundle;
use yii\web\AssetManager;
use zetsoft\system\Az;
use zetsoft\system\helpers\ZArrayHelper;
use zetsoft\system\helpers\ZFileHelper;
use zetsoft\system\helpers\ZStringHelper;



class ZAssetManager extends AssetManager
{
    private $vendorsUrl = '@web/vendors';
    private $vendorsPath = '@root/vendors';

    private $vendorPath = '@root/vendor';

    public function init()
    {
        $this->vendorsPath = Az::getAliasNorm($this->vendorsPath);
        $this->vendorPath = Az::getAliasNorm($this->vendorPath);
        $this->vendorsUrl = Az::getAliasNorm($this->vendorsUrl);

    }


    public function getAssetUrl($bundle, $asset)
    {


        $source = ZFileHelper::normLinux($bundle->sourcePath);

        $source = strtr($source, [
            $this->vendorsPath => '',
            $this->vendorPath => '',
        ]);


        $basePath = $this->vendorsPath . $source;
        $baseUrl = $this->vendorsUrl . $source;


        switch (true) {

            case    !Url::isRelative($asset) || strncmp($asset, '/', 1) === 0:
                $return = $asset;
                break;

            case $this->appendTimestamp && ($timestamp = @filemtime("$basePath/$asset")) > 0:
                $return = "$baseUrl/$asset?v=$timestamp";
                break;

            default:
                $return = "$baseUrl/$asset";
                break;

        }

        /*
         * http://mplace.zoft.uz/vendors/kernel/yiisofts/vendor/yiisoft/yii2/debug/assets/css/main.css
         * http://mplace.zoft.uz/vendors/kernel/yiisofts/vendor/yiisoft/yii2-debug/src/assets/css/main.css
         *
         * */

     //   $return= 'abl.js';

        if (!ZStringHelper::find($return, 'yiisofts'))
            $return = strtr($return, [
                '/vendors' => '/vendors/kernel/yiisofts/vendor',
            ]);

        if (ZStringHelper::find($return, '.js'))
            Az::$app->cores->auth->headerJs($return);

       if (ZStringHelper::find($return, '.css'))
            Az::$app->cores->auth->headerCss($return);


        Az::$app->cores->auth->linkAll();

          //vdd( $return);
        return $return;
    }


    public function publish($path, $options = [])
    {
        return null;
    }

    protected function publishDirectory($src, $options)
    {
    }

    protected function publishFile($src)
    {
    }

}
