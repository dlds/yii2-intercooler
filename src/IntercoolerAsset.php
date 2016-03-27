<?php
/**
 * @link http://www.digitaldeals.cz/
 * @copyright Copyright (c) 2016 Digital Deals s.r.o.
 * @license http://www.digitaldeals.cz/license/
 */

namespace dlds\intercooler;

use yii\web\AssetBundle;

/**
 * @author Jiri Svoboda <jiri.svobodao@dlds.cz>
 * @package intercooler
 * @see http://intercoolerjs.org/docs.html
 */
class IntercoolerAsset extends AssetBundle {

    public $sourcePath = '@bower/intercooler-js/src';
    public $css = [];
    public $js = [
        'intercooler.js'
    ];
    public $depends = [
        'yii\web\JqueryAsset',
    ];

    public function init()
    {
        parent::init();

        if (YII_DEBUG)
        {
            $this->js[] = 'intercooler-debugger.js';
        }
    }
}