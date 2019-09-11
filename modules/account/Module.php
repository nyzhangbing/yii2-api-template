<?php

namespace app\modules\account;

use app\core\QcModule;

/**
 * fund module definition class
 */
class Module extends QcModule
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\account\controllers';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        // custom initialization code goes here
    }
}
