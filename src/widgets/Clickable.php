<?php
/**
 * @link http://www.digitaldeals.cz/
 * @copyright Copyright (c) 2016 Digital Deals s.r.o.
 * @license http://www.digitaldeals.cz/license/
 */

namespace dlds\intercooler\widgets;

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use dlds\intercooler\Intercooler;

/**
 * This is the main class of the Clickable widget
 *
 * @author Jiri Svoboda <jiri.svobodao@dlds.cz>
 * @package lazyload
 */
class Clickable extends \yii\base\Widget {

    /**
     * @var string wrapper tag
     */
    public $wrapper = 'div';

    /**
     * @var string additional content
     */
    public $content;

    /**
     * @var array additional wrapper options
     */
    public $options = [];

    /**
     * @var array additional wrapper options
     */
    public $intercooler = [];

    /**
     * @var Intercooler instance
     */
    protected $_handler;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->_handler = new Intercooler($this->intercooler);

        echo Html::beginTag($this->wrapper, $this->initOptions());
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        if ($this->content)
        {
            echo $this->content;
        }

        echo Html::endTag($this->wrapper);
    }

    /**
     * Initalizes and retrieves all required options for intercooler and user defined options
     */
    protected function initOptions()
    {
        $options = ArrayHelper::merge($this->_handler->getOptions($this->id), $this->options);

        return $options;
    }
}