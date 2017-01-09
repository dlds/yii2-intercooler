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
 * This is Infinite List widget which enhaces standart ListView widget.
 * ---
 * Infinite list works with ajax loaded content to the end of current list
 * based on user interaction.
 * ---
 *
 * @author Jiri Svoboda <jiri.svoboda@dlds.cz>
 * @package intercooler
 */
class InfiniteList extends \yii\widgets\ListView
{

    /**
     * Element keys
     */
    const KEY_INDICATOR_REFRESH = 'il-indicator-refresh';
    const KEY_INDICATOR_EMPTY = 'il-indicator-empty';

    /**
     * @var string partail layout which will be used when partial output is required
     */
    public $partialLayout = "{items}{pager}";

    /**
     * @var string indicator refresh html
     */
    public $indicatorRefresh;

    /**
     * @var array custom sections
     */
    public $sections = [];

    /**
     * @var array intercooler config
     */
    public $intercooler = [];

    /**
     * @var \dlds\intercooler\Intercooler instance
     */
    protected $_handler;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->_handler = new Intercooler($this->intercooler);

        $this->initListOptions();
    }

    /**
     * Inits all required list options together with intercooler options
     */
    public function initListOptions()
    {
        $options = \yii\helpers\ArrayHelper::merge($this->_handler->getOptions($this->id), $this->options);

        if ($this->indicatorRefresh) {
            $options[Intercooler::attr(Intercooler::ATTR_INDICATOR)] = $this->getIndicatorRefreshId(true);
        }

        $this->options = $options;
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        // use parent rendering when it is first (not partial) request
        if (!static::isPartial($this->id)) {
            return parent::run();
        }

        // print partial list
        return $this->listPartial();
    }

    /**
     * Prints partial list
     * ---
     * Use detected partial layout and renders customized partial list
     * ---
     * @see http://php.net/manual/en/function.preg-replace-callback.php
     * ---
     * @return string
     */
    public function listPartial()
    {
        echo preg_replace_callback("/{\\w+}/", function ($matches) {
            $content = $this->renderSection($matches[0]);

            return $content === false ? $matches[0] : $content;
        }, $this->detectLayout($this->id));
    }

    /**
     * @inheritdoc
     */
    public function renderSection($name)
    {
        switch ($name) {
            case '{indicatorRefresh}':
                return $this->renderIndicatorRefresh();
            default:
                return $this->renderCustomSection($name);
        }
    }

    /**
     * Renders a custom section if is specified otherwise calls parent render method
     * @param string $name the section name, e.g., `{summary}`, `{items}`.
     * @return string|boolean the rendering result of the section, or false if the named section is not supported.
     */
    public function renderCustomSection($name)
    {
        if (isset($this->sections[$name])) {
            return $this->sections[$name] ? $this->sections[$name] : '';
        }

        return parent::renderSection($name);
    }

    /**
     * @inheritdoc
     */
    public function renderEmpty()
    {
        $empty = ArrayHelper::getValue($this->pager, 'indicatorEmpty');

        if (!$empty) {
            $empty = parent::renderEmpty();
        }

        $html = Html::tag('div', $empty, [
                'id' => $this->getIndicatorEmptyId(),
                'class' => self::KEY_INDICATOR_EMPTY,
        ]);

        $html .= $this->renderIndicatorRefresh();

        return $html;
    }

    /**
     * Retrieves refresh indicator html
     * @return string
     */
    protected function renderIndicatorRefresh()
    {
        return Html::tag('div', $this->indicatorRefresh, [
                'id' => $this->getIndicatorRefreshId(),
                'class' => self::KEY_INDICATOR_REFRESH,
                'style' => 'display: none'
        ]);
    }

    /**
     * Retrieves empty indicator unique identification
     * @return string
     */
    protected function getIndicatorEmptyId($withHash = false)
    {
        return static::id($this->id, self::KEY_INDICATOR_EMPTY, $withHash);
    }

    /**
     * Retrieves refresh indicator unique identification
     * @return string
     */
    protected function getIndicatorRefreshId($withHash = false)
    {
        return static::id($this->id, self::KEY_INDICATOR_REFRESH, $withHash);
    }

    /**
     * Detects layout which will be used
     */
    protected function detectLayout($id)
    {
        if (static::isPartial($id) && !static::isRefresh($id)) {
            return $this->partialLayout;
        }

        return $this->layout;
    }

    /**
     * Indicates if current request is partial (next page)
     * @return boolean
     */
    public static function isPartial($id)
    {
        $isPartial = \Yii::$app->request->get(InfiniteListPager::QP_PARTIAL_OUTPUT, false);

        if (!$isPartial) {
            $isPartial = static::isRefresh($id);
        }

        return (boolean) $isPartial;
    }

    /**
     * Indicates if current request is refresh
     * @return boolean
     */
    public static function isRefresh($id)
    {
        $trigger = \Yii::$app->request->get(Intercooler::attr(Intercooler::QP_TRIGGER), false);

        return $id == $trigger;
    }

    /**
     * Retrieves infinite element html ID
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
