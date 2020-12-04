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


use BotMan\BotMan\Messages\Conversations\Conversation;
use zetsoft\system\control\ZCoreTrait;

class ZConversation extends Conversation
{

    use ZCoreTrait;

    public function __construct()
    {
        $this->trait();
    }

}
