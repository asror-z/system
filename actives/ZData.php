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


use zetsoft\system\kernels\ZFrame;

class ZData extends ZFrame
{

    public $class;

    /** @var ZData $object */
    public $object;

    public function init()
    {
        parent::init();
        if (!empty($this->class))
            $this->object = new $this->class();
    }


    public function lang(): array
    {
        return [];
    }

    public function name(): array
    {
        $lang = $this->lang();
        return array_keys($lang);
    }

    public function menu(): array
    {
        return [
            'ALL' => $this->urlGetBase()
        ];

    }


}
