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
 * This is the main class of the Polling widget
 *
 * @author Jiri Svoboda <jiri.svobodao@dlds.cz>
 * @package lazyload
 */
class Polling extends \dlds\intercooler\Intercooler {

    /**
     * Polling attributes
     */
    const ATTR_INTERVAL = 'poll';

    /**
     * @var int polling interval
     */
    public $interval;

    /**
     * @var int maximum requests for given element
     */
    public $repeats;

    /**
     * @var boolean indicates if polling will be paused
     */
    public $pause = false;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->type = self::RQ_TYPE_SRC;

        return parent::init();
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        return parent::run();
    }

    /**
     * Retrieves wrapper options based on widget config
     * @return array
     */
    protected function getWrapperOptions()
    {
        $options = parent::getWrapperOptions();

        if ($interval)
        {
            $options[self::getAttrName(self::ATTR_INTERVAL)] = $interval;
        }

        return $options;
    }
}