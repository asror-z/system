<?php

/**
 *
 *
 * Author:  Asror Zakirov
 * https://www.linkedin.com/in/asror-zakirov
 * https://github.com/asror-z
 *
 */

namespace zetsoft\system\targets;


use BotMan\Drivers\Telegram\TelegramDriver;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ParameterBag;
use Illuminate\Support\Collection;

class ZTelegramDriver extends TelegramDriver
{
    protected function isValidLoginRequest()
    {
        return parent::isValidLoginRequest();
    }

    public function buildPayload(Request $request)
    {
        $this->payload = new ParameterBag((array)json_decode($request->getContent(), true));

        $message = $this->payload->get('message');
        if (empty($message)) {
            $message = $this->payload->get('edited_message');
        }
        $this->event = Collection::make($message);
        $this->config = Collection::make($this->config->get('telegram'));

        $this->queryParameters = Collection::make($request->query);
        $this->queryParameters = new Collection();
        //vdd($this->queryParameters);
        
    }


}
