<?php

/**
 * @link http://www.digitaldeals.cz/
 * @copyright Copyright (c) 2016 Digital Deals s.r.o.
 * @license http://www.digitaldeals.cz/license/
 * @author Jiri Svoboda <jiri.svoboda@dlds.cz>
 */

namespace dlds\intercooler\widgets;

use yii\helpers\Html;
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
class AjaxHistory extends \yii\base\Widget
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
     * @inheritdoc
     */
    public function init()
    {
        $this->options[Intercooler::attr(Intercooler::ATTR_HISTORY)] = true;

        echo Html::beginTag($this->wrapper, $this->options);
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

}
