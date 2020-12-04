<?php

/**
 *
 *
 * Author:  Asror Zakirov
 *
 * https://www.linkedin.com/in/asror-zakirov
 * https://www.facebook.com/asror.zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\system\behave;



use zetsoft\system\Az;
use zetsoft\system\control\ZCoreTrait;
use zetsoft\system\kernels\ZTrait;
use yii\behaviors\AttributeBehavior;


/**
 * Class    ZAttributeBehavior
 * @package zetsoft\system\behave
 *
 * 
 */
class ZAttributeBehavior extends AttributeBehavior
{

    use ZCoreTrait;

	public function init()
	{
		parent::init();
        $this->trait();

	}

}
