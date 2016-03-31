<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace dlds\intercooler\widgets\infinite;

use yii\helpers\Html;
use dlds\intercooler\Intercooler;

/**
 * The ListView widget is used to display data from data
 * provider. Each data model is rendered using the view
 * specified.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class InfiniteList extends \yii\widgets\ListView {

    /**
     * Element keys
     */
    const KEY_INDICATOR_REFRESH = 'il-indicator-refresh';

    /**
     * @var string partail layout which will be used when partial output is required
     */
    public $partialLayout;

    /**
     * @var string indicator refresh html
     */
    public $indicatorRefresh;

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

        if ($this->indicatorRefresh)
        {
            $options[Intercooler::getAttrName(Intercooler::ATTR_INDICATOR)] = sprintf('#%s', $this->getIndicatorRefreshId());
        }

        $this->options = $options;
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        if (self::isPartial($this->id))
        {
            if ($this->showOnEmpty || $this->dataProvider->getCount() > 0)
            {
                $content = preg_replace_callback("/{\\w+}/", function ($matches) {
                    $content = $this->renderSection($matches[0]);

                    return $content === false ? $matches[0] : $content;
                }, $this->detectLayout($this->id));
            }
            else
            {
                $content = $this->renderEmpty();
            }

            echo $content;
        }
        else
        {
            return parent::run();
        }
    }

    /**
     * Renders a section of the specified name.
     * If the named section is not supported, false will be returned.
     * @param string $name the section name, e.g., `{summary}`, `{items}`.
     * @return string|boolean the rendering result of the section, or false if the named section is not supported.
     */
    public function renderSection($name)
    {
        switch ($name)
        {
            case '{summary}':
                return $this->renderSummary();
            case '{indicatorRefresh}':
                return $this->renderIndicatorRefresh();
            case '{items}':
                return $this->renderItems();
            case '{pager}':
                return $this->renderPager();
            case '{sorter}':
                return $this->renderSorter();
            default:
                return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function renderEmpty()
    {
        if ($this->pager && isset($this->pager['indicatorEmpty']))
        {
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
    protected function getIndicatorRefreshId()
    {
        return sprintf('%s-%s', $this->id, self::KEY_INDICATOR_REFRESH);
    }

    /**
     * Detects layout which will be used
     */
    protected function detectLayout($id)
    {
        if (self::isPartial($id) && !self::isRefresh($id))
        {
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

        if (!$isPartial)
        {
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
}