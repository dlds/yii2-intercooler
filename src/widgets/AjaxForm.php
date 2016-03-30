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
 * This is the main class of the AjaxForm widget
 *
 * @author Jiri Svoboda <jiri.svobodao@dlds.cz>
 * @package lazyload
 */
class AjaxForm extends \yii\widgets\ActiveForm {

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

        if (!isset($this->options['id']))
        {
            $this->options['id'] = $this->getId();
        }

        echo Html::beginTag('form', $this->initOptions());
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        if (!empty($this->_fields))
        {
            throw new InvalidCallException('Each beginField() should have a matching endField() call.');
        }

        echo Html::endForm();
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