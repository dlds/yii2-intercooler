<?php

/**
 * @link http://www.digitaldeals.cz/
 * @copyright Copyright (c) 2016 Digital Deals s.r.o.
 * @license http://www.digitaldeals.cz/license/
 * @author Jiri Svoboda <jiri.svoboda@dlds.cz>
 */

namespace dlds\intercooler\widgets\infinite;

use yii\helpers\Html;
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
            $options[Intercooler::getAttrName(Intercooler::ATTR_INDICATOR)] = $this->getIndicatorRefreshId(true);
        }

        $this->options = $options;
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        if (self::isPartial($this->id)) {
            if ($this->showOnEmpty || $this->dataProvider->getCount() > 0) {
                $content = preg_replace_callback("/{\\w+}/", function ($matches) {
                    $content = $this->renderSection($matches[0]);

                    return $content === false ? $matches[0] : $content;
                }, $this->detectLayout($this->id));
            } else {
                $content = $this->renderEmpty();
            }

            echo $content;
        } else {
            return parent::run();
        }
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
        if ($this->pager && isset($this->pager['indicatorEmpty'])) {
            return $this->pager['indicatorEmpty'];
        }

        return parent::renderEmpty();
    }

    /**
     * Retrieves refresh indicator html
     * @return string
     */
    protected function renderIndicatorRefresh()
    {
        return Html::tag('div', $this->indicatorRefresh, [
                'id' => $this->getIndicatorRefreshId(),
                'class' => 'ic-indicator',
                'style' => 'display: none'
        ]);
    }

    /**
     * Retrieves refresh indicator unique identificaiton
     * @return string
     */
    protected function getIndicatorRefreshId($hash = false)
    {
        return self::getElementId($this->id, self::KEY_INDICATOR_REFRESH, $hash);
    }

    /**
     * Detects layout which will be used
     */
    protected function detectLayout($id)
    {
        if (self::isPartial($id) && !self::isRefresh($id)) {
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
            $isPartial = self::isRefresh($id);
        }

        return (boolean) $isPartial;
    }

    /**
     * Indicates if current request is refresh
     * @return boolean
     */
    public static function isRefresh($id)
    {
        $trigger = \Yii::$app->request->get(Intercooler::getAttrName(Intercooler::QP_TRIGGER), false);

        return $id == $trigger;
    }

    /**
     * Retrieves element id
     * @param string $id
     * @param string $suffix
     * @return string
     */
    public static function getElementId($id, $suffix, $hash = false)
    {
        if ($hash) {
            return sprintf('#%s-%s', $id, $suffix);
        }

        return sprintf('%s-%s', $id, $suffix);
    }

}
