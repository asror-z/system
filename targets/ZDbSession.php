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


use yii\db\Query;
use yii\web\DbSession;
use zetsoft\models\core\CoreSession;

class ZDbSession extends DbSession
{
    public function readSession($id)
    {
        return null;
    }

    public function writeSession($id, $data)
    {
        try {
            // ensure backwards compatability (fixed #9438)
            if ($this->writeCallback && !$this->fields) {
                $this->fields = $this->composeFields();
            }
            // ensure data consistency
            if (!isset($this->fields['name'])) {
                $this->fields['name'] = $data['value'];
            } else {
                $_SESSION[$this->fields['name']] = $this->fields['value'];
            }
            // ensure 'id' and 'expire' are never affected by [[writeCallback]]
            $this->fields = array_merge($this->fields, [
                'sess' => $id,
                'expire' => time() + $this->getTimeout(),
            ]);
            //$this->fields = $this->typecastFields($this->fields);
            $this->db->createCommand()->upsert($this->sessionTable, $this->fields)->execute();
            $this->fields = [];
        } catch (\Exception $e) {
            \Yii::$app->errorHandler->handleException($e);
            return false;
        }
        return true;
    }

    /**
     * Function  composeFields
     * @param null $id
     * @param null $data
     * @return  array|mixed
     */
    protected function composeFields($id = null, $data = null)
    {
        $fields = $this->writeCallback ? call_user_func($this->writeCallback, $this) : [];
        if ($id !== null) {
            $fields['user_id'] = $id;
        }
        if ($data !== null) {
            $fields['name'] = $data['name'];
            $fields['value'] = $data['value'];
        }

        return $fields;
    }

}
