<?php
/**
 * @link http://www.digitaldeals.cz/
 * @copyright Copyright (c) 2016 Digital Deals s.r.o.
 * @license http://www.digitaldeals.cz/license/
 */

namespace dlds\intercooler\widgets;

use yii\helpers\Html;
use yii\helpers\ArrayHelper;

/**
 * This is the main class of the LazyPjax widget
 *
 * @author Jiri Svoboda <jiri.svobodao@dlds.cz>
 * @package lazyload
 */
class LazilyLoad extends \dlds\intercooler\Intercooler {

    /**
     * @var string inital html before lazily load is done
     */
    public $loadingHtml;

    /**
     * @var string fallback html if lazily load failed
     */
    public $fallbackHtml;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if ($this->loadingHtml)
        {
            echo $this->loadingHtml;
        }
    }
}