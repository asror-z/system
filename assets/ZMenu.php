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

namespace zetsoft\system\assets;


use yii\helpers\FileHelper;
use zetsoft\dbitem\wdg\MenuItem;
use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;

class ZMenu extends ZFrame
{
    public $options = [];
    public $items = [];
    public $optionsALL = [];

    public $path;

    public function add(string $label)
    {
        if (!empty($this->options))
            $this->optionsALL[] = [
                'label' => $label,
                'items' => $this->options,
            ];

        return true;
    }

    #region Render


    protected function allChilds($path)
    {
        $folders = $this->folderChilds($path);
        $files = $this->fileChilds($path);
        return array_merge($folders, $files);
    }

    protected function folderChilds($path)
    {

        $basePath = bname($path);
        if ($basePath === '@')
            return [];

        $folder = FileHelper::findDirectories($path, ['recursive' => false]);
        $folder = $this->folderArrayNormalize($folder);
        return $folder;
    }

    protected function fileChilds($path)
    {
        $files = FileHelper::findFiles($path, [
            'only' => ['*.php'],
            'recursive' => false]);
        $files = $this->fileArrayNormalize($files);
        return $files;
    }

    protected function fileArrayNormalize($arr)
    {
        $result = [];
        foreach ($arr as $item) {
            $pos = strrpos($item, '\\');
            $resultLabel = substr($item, $pos + 1);

            $item = FileHelper::normalizePath($item);
            $item = $this->_returnUrl($item);
            $menuItem = new MenuItem();

            $menuItem->icon = Az::$app->utility->mains->icon();
            $menuItem->title = $resultLabel;
            $menuItem->url = '/core/tester/asror/main.aspx?path=' . $item;
            $result[] = $menuItem;
        }

        return $result;
    }

    protected function folderArrayNormalize($arr)
    {
        $result = [];
        foreach ($arr as $item) {
            $pos = strrpos($item, '\\');
            $resultLabel = substr($item, $pos + 1);

            $item = $this->allChilds($item);
            $menuItem = new MenuItem();

            $menuItem->icon = Az::$app->utility->mains->icon();
            $menuItem->title = $resultLabel;
            $menuItem->items = $item;
            $result[] = $menuItem;

        }

        return $result;
    }

    protected function _returnUrl($path)
    {
        $pos = strpos($path, '\render');
        $item = substr($path, $pos + 1);

        $replace = ['.php', Root . '\webhtm\ALL', '/'];
        $item = str_replace($replace, '', $item);


        return $item;
    }

    #endregion

}
