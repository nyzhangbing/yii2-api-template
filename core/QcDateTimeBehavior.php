<?php

namespace app\core;

use yii\behaviors\AttributeBehavior;
use yii\db\{
    BaseActiveRecord, Expression
};

class QcDateTimeBehavior extends AttributeBehavior
{
    public $createdAtAttribute = 'created_time';

    public $updatedAtAttribute = 'updated_time';

    public $value;

    public function init()
    {
        parent::init();

        if (empty($this->attributes)) {
            $this->attributes = [
                BaseActiveRecord::EVENT_BEFORE_INSERT => [$this->createdAtAttribute, $this->updatedAtAttribute],
                BaseActiveRecord::EVENT_BEFORE_UPDATE => $this->updatedAtAttribute,
            ];
        }
    }

    protected function getValue($event)
    {
        if ($this->value instanceof Expression) {
            return $this->value;
        } else {
            return $this->value !== null ? call_user_func($this->value, $event) : date("Y-m-d H:i:s");
        }
    }

    public function touch($attribute)
    {
        $this->owner->updateAttributes(array_fill_keys((array)$attribute, $this->getValue(null)));
    }
}