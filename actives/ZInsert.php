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


use zetsoft\system\Az;
use zetsoft\system\kernels\ZFrame;
use zetsoft\system\module\Models;

class ZInsert extends ZFrame
{

    public function save()
    {
        $this->model->configs->rules = validatorSafe;

        Az::debug($this->model->attributes, '# Inserting | ' . $this->model::className().' : ');


        $this->model->columns();

        try {
            $this->model->save();
        } catch (\Exception $exception) {
            vd($exception->getMessage());
        }


    }

}
