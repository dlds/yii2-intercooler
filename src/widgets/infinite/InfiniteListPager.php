<?php

/**
 * @link http://www.digitaldeals.cz/
 * @copyright Copyright (c) 2016 Digital Deals s.r.o.
 * @license http://www.digitaldeals.cz/license/
 * @author Jiri Svoboda <jiri.svoboda@dlds.cz>
 */

namespace dlds\intercooler\widgets\infinite;

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use dlds\intercooler\Intercooler;

/**
 * This is the main class of the InfiniteListPager widget
 * ---
 * Pager is used together with InfiniteList to handling paging and
 * storing info about current page.
 * ---
 * @see InfiniteList
 * @author Jiri Svoboda <jiri.svobodao@dlds.cz>
 * @package intercooler
 */
class InfiniteListPager extends \yii\widgets\LinkPager
{

    /**
     * Behaviors types
     */
    const B_ON_CLICK = 'click';
    const B_ON_SCROLL = 'scroll';
    const B_ON_CUSTOM = 'custom';

    /**
     * InfiniteListPager attributes
     */
    const ATTR_REPLACE_TARGET = 'replace-target';

    /**
     * Query params
     */
    const QP_PARTIAL_OUTPUT = 'partial-output';

    /**
     * Element key
     */
    const KEY_PAGER = 'il-pager';
    const KEY_TRIGGER = 'il-trigger';
    const KEY_INDICATOR_LOADING = 'il-indicator-loading';
    const KEY_INDICATOR_ERROR = 'il-indicator-error';

    /**
     * @var string behavior type
     */
    public $behavior = self::B_ON_CLICK;

    /**
     * @var boolean indicates if whole target content will be replaced or not
     */
    public $replaceTarget;

    /**
     * @var string loading indicator
     */
    public $indicatorLoading;

    /**
     * @var string loading indicator options
     */
    public $indicatorLoadingOptions = [];

    /**
     * @var string empty indicator
     */
    public $indicatorEmpty;

    /**
     * @var string all done indicator
     */
    public $indicatorDone;

    /**
     * @var string error indicator
     */
    public $indicatorError;

    /**
     * @var string wrapper tag
     */
    public $wrapper = 'div';

    /**
     * @var array intercooler config
     */
    public $intercooler = [];

    /**
     * @var array|null pagination route
     */
    public $route = null;

    /**
     * @var \dlds\intercooler\Intercooler instance
     */
    protected $_handler;

    /**
     * @inheridoc
     */
    public function init()
    {
        parent::init();

        $this->initPager();

        echo Html::beginTag($this->wrapper, $this->options);
    }

    /**
     * @inheridoc
     */
    public function run()
    {
        parent::run();

        echo Html::endTag($this->wrapper);
    }

    /**
     * Inits pager by preddfined config based on curren behavior
     * @param type $param
     */
    public function initPager()
    {
        if (!isset($this->options['id'])) {
            $this->options['id'] = static::id($this->id, self::KEY_PAGER);
        }

        Html::addCssClass($this->options, self::KEY_PAGER);

        if ($this->route) {
            $this->pagination->route = ArrayHelper::getValue($this->route, 0);
        }

        $this->_handler = new \dlds\intercooler\Intercooler($this->intercooler);
        $this->_handler->url = $this->pagination->createUrl($this->getNextPage());

        $this->_handler->include = \yii\helpers\Json::encode([self::QP_PARTIAL_OUTPUT => 1]);

        if (self::B_ON_CLICK === $this->behavior) {
            $this->_handler->type = Intercooler::RQ_TYPE_GET;
            $this->_handler->target = sprintf('#%s', $this->options['id']);
            $this->_handler->indicator = sprintf('#%s', $this->getIndicatorLoadingId());
            $this->replaceTarget = true;
        }

        if (self::B_ON_SCROLL === $this->behavior) {
            $this->_handler->type = Intercooler::RQ_TYPE_APPEND;
            $this->_handler->trigger = Intercooler::EVENT_ON_SCROLLED_INTO_VIEW;
            $this->_handler->indicator = sprintf('#%s', $this->getIndicatorLoadingId());
            $this->_handler->target = 'closest div';
        }
    }

    /**
     * @inheritdoc
     */
    protected function renderPageButtons()
    {
        $total = $this->getTotalPageCount();

        if ($total < 2) {
            if (!$this->hideOnSinglePage) {
                return $this->indicatorDone;
            }

            return null;
        }

        $isDisabled = $this->pagination->getPage() >= ($total - 1);

        echo $this->renderPageButton($this->nextPageLabel, $this->getNextPage(), $this->nextPageCssClass, $isDisabled, true);
    }

    /**
     * @inheritdoc
     */
    protected function renderPageButton($label, $page, $class, $disabled, $active)
    {
        $options = $this->getPageBtnOptions($page);

        if (!$disabled) {
            $html = $this->getIndicatorError();

            $content = $this->getIndicatorLoading();

            $content .= $label;

            $html .= Html::button($content, $options);

            return $html;
        }

        return $this->indicatorDone;
    }

    /**
     * Retrieves all required page button options
     * @return array
     */
    protected function getPageBtnOptions($page)
    {
        $additionals = [
            Intercooler::attr(self::ATTR_REPLACE_TARGET) => $this->replaceTarget ? "true" : "false",
        ];

        if ($this->indicatorError) {
            $additionals[Intercooler::attr(Intercooler::ATTR_EVT_ON_ERROR)] = new \yii\web\JsExpression("var e = document.querySelector('#$this->indicatorErrorId'); if (typeof(e) != 'undefined' && e != null) {e.style.display = null;}");
            $additionals[Intercooler::attr(Intercooler::ATTR_EVT_ON_BEFORE_SEND)] = new \yii\web\JsExpression("var icf = document.querySelector('#$this->indicatorErrorId'); if (typeof(icf) != 'undefined' && icf != null) {icf.style.display = 'none';} var icl = document.querySelector('#$this->indicatorLoadingId'); if (typeof(icl) != 'undefined' && icl != null) {icl.style.display = null;}");
            $additionals[Intercooler::attr(Intercooler::ATTR_EVT_ON_COMPLETE)] = new \yii\web\JsExpression("var e = document.querySelector('#$this->indicatorLoadingId'); if (typeof(e) != 'undefined' && e != null) {e.style.display = 'none';}");
        }

        $js = new \yii\web\JsExpression("jQuery('#{$this->id}').on('success.ic', function(e) {console.log(e)})");
        $this->getView()->registerJs($js);

        return ArrayHelper::merge($this->_handler->getOptions($this->getTriggerId(), $additionals), $this->linkOptions);
    }

    /**
     * Retrieves pager unique identificaiton
     * @return string
     */
    protected function getTriggerId()
    {
        return static::id($this->id, self::KEY_TRIGGER);
    }

    /**
     * Retrieves loading indicator unique identificaiton
     * @return string
     */
    protected function getIndicatorLoadingId()
    {
        return static::id($this->id, self::KEY_INDICATOR_LOADING);
    }

    /**
     * Retrieves error indicator unique identificaiton
     * @return string
     */
    protected function getIndicatorErrorId()
    {
        return static::id($this->id, self::KEY_INDICATOR_ERROR);
    }

    /**
     * Retrieves loading indicator html
     * @return string
     */
    protected function getIndicatorLoading()
    {
        $class = ArrayHelper::remove($this->indicatorLoadingOptions, 'class');

        $options = [
            'id' => $this->getIndicatorLoadingId(),
            'class' => self::KEY_INDICATOR_LOADING,
            'style' => 'display: none'
        ];

        if ($class) {
            Html::addCssClass($options, $class);
        }

        return Html::tag('div', $this->indicatorLoading, ArrayHelper::merge($options, $this->indicatorLoadingOptions));
    }

    /**
     * Retrieves loading indicator html
     * @return string
     */
    protected function getIndicatorError()
    {
        return Html::tag('div', $this->indicatorError, [
                'id' => $this->getIndicatorErrorId(),
                'class' => self::KEY_INDICATOR_ERROR,
                'style' => 'display: none'
        ]);
    }

    /**
     * Retrieves next page
     * @return int
     */
    protected function getNextPage()
    {
        $current = $this->pagination->getPage();

        $next = $current + 1;

        $total = $this->getTotalPageCount();

        if ($next >= $total - 1) {
            $next = $total - 1;
        }

        return $next;
    }

    /**
     * Retrieves total pages count
     * @return int
     */
    protected function getTotalPageCount()
    {
        return $this->pagination->getPageCount();
    }

    /**
     * Retrieves infinite pager element html ID
     * @param string $id
     * @param string $suffix
     * @param boolean $withHash
     * @return string
     */
    public static function id($id, $suffix, $withHash = false)
    {
        $id = sprintf('%s-%s', $id, $suffix);

        if (!$withHash) {
            return $id;
        }

        return sprintf('#%s', $id);
    }

}
