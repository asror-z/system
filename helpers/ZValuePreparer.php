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
namespace zetsoft\system\helpers;

use unclead\multipleinput\components\ValuePreparer;
use yii\base\Model;
use yii\db\ActiveRecordInterface;
use yii\helpers\ArrayHelper;

class ZValuePreparer extends ValuePreparer
{
    /**
     * @param $data Prepared data
     *
     * @return int|mixed|null|string
     */
    public function prepare($data)
    {
        $value = null;
        if ($data instanceof ActiveRecordInterface) {
            if ($data->canGetProperty($this->name)) {
                $value = $data->{$this->name};
            } else {
                $relation = $data->getRelation($this->name, false);
                if ($relation !== null) {
                    $value = $relation->findFor($this->name, $data);
                } else {
                    $value = $data->{$this->name};
                }
            }
        } elseif ($data instanceof Model) {
            $value = $data->{$this->name};
        } elseif (is_array($data)) {
            $value = ArrayHelper::getValue($data, $this->name, null);
        } elseif(is_string($data) || is_numeric($data)) {
            $value = $data;
        }

        if ($this->defaultValue !== null && $this->isEmpty($value)) {
            $value = $this->defaultValue;
        }

        return $value;
    }
}
