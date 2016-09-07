<?php

/**
 * @link http://www.digitaldeals.cz/
 * @copyright Copyright (c) 2016 Digital Deals s.r.o.
 * @license http://www.digitaldeals.cz/license/
 * @author Jiri Svoboda <jiri.svoboda@dlds.cz>
 */

namespace dlds\intercooler\widgets;

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use dlds\intercooler\Intercooler;

/**
 * This is the main class of the Ajax Block widget
 * ---
 * Used for easy init intercooler on html element
 * ---
 *
 * @author Jiri Svoboda <jiri.svobodao@dlds.cz>
 * @package lazyload
 */
class AjaxBlock extends \yii\base\Widget
{

    /**
     * @var string wrapper tag
     */
    public $wrapper = 'div';

    /**
     * @var string additional content
     */
    public $content;

    /**
     * @var array additional wrapper html options
     */
    public $options = [];

    /**
     * @var string inital html before lazily load is done
     */
    public $loadingHtml;

    /**
     * @var string fallback html if lazily load failed
     */
    public $fallbackHtml;

    /**
     * @var array additional wrapper html options
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

        if ($this->loadingHtml) {
            echo Html::tag('div', $this->loadingHtml, [
                'class' => 'ic-loading ic-ntc ic-indicator',
            ]);
        }

        if ($this->fallbackHtml) {
            echo Html::tag('div', $this->fallbackHtml, [
                'class' => 'ic-fallback ic-ntc',
                'style' => 'display:none',
            ]);
        }
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        if ($this->content) {
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

        if ($this->fallbackHtml) {
            $options[Intercooler::attr(Intercooler::ATTR_EVT_ON_ERROR)] = new \yii\web\JsExpression("var e = document.querySelector('#$this->id .ic-fallback'); if (typeof(e) != 'undefined' && e != null) {e.style.display = null;}");
            $options[Intercooler::attr(Intercooler::ATTR_EVT_ON_BEFORE_SEND)] = new \yii\web\JsExpression("var icf = document.querySelector('#$this->id .ic-fallback'); if (typeof(icf) != 'undefined' && icf != null) {icf.style.display = 'none';} var icl = document.querySelector('#$this->id .ic-loading'); if (typeof(icl) != 'undefined' && icl != null) {icl.style.display = null;}");
            $options[Intercooler::attr(Intercooler::ATTR_EVT_ON_COMPLETE)] = new \yii\web\JsExpression("var e = document.querySelector('#$this->id .ic-loading'); if (typeof(e) != 'undefined' && e != null) {e.style.display = 'none';}");
        }

        if ($this->_handler->indicator) {
            // hides request indicator after document is ready
            $js = new \yii\web\JsExpression("var e = document.querySelector('{$this->_handler->indicator}'); if (typeof(e) != 'undefined' && e != null) {e.style.display = 'none';}");
            $this->getView()->registerJs($js);
        }

        return $options;
    }

}
