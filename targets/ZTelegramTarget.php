<?php

namespace zetsoft\system\targets;

use zetsoft\system\Az;

/**
 * Yii 2.0 Telegram Log Target
 * AsrorZTelegramTarget sends selected log messages to the specified telegram chats or channels
 *
 * You should set (telegram bot token)[https://core.telegram.org/bots#botfather] and chatId in your config file like below code:
 * ```php
 * 'log' => [
 *     'target' => [
 *         [
 *             'class' => 'airani\log\AsrorZTelegramTarget',
 *             'levels' => ['error'],
 *             'botToken' => '123456:abcde', // bot token secret key
 *             'chatId' => '123456', // chat id or channel username with @ like 12345 or @channel
 *         ],
 *     ],
 * ],
 * ```
 *
 * @author Ali Irani <ali@irani.im>
 */
class ZTelegramTarget extends ZTarget
{

    /**
     * Destination chat id or channel username
     * @var int|string
     */
    public $chatId;


    /**
     * Exports log [[messages]] to a specific destination.
     * Child classes must implement this method.
     */
    public function export()
    {
        $sRelMessage = parent::export();
        /*
        Az::$app->telegram->token = '280253273:AAG5oiNEFPvTpy8LdnX4RPL1reeZCVx4uKM';
        $return = Az::$app->telegram->sendMessage(
            $this->chatId,
            $sRelMessage
        );
        */

        $return  = Az::$app->utility->telegramasync->sendTelegram('280253273:AAG5oiNEFPvTpy8LdnX4RPL1reeZCVx4uKM', $this->chatId,$sRelMessage);


    }

    /**
     * Formats a log message for display as a string.
     * @param array $message the log message to be formatted.
     * The message structure follows that in [[Logger::messages]].
     * @return string the formatted message
     */
    public function formatMessage($message)
    {
        return parent::formatMessage($message);
    }

}
