<?php
/**
 * @link http://www.digitaldeals.cz/
 * @copyright Copyright (c) 2016 Digital Deals s.r.o.
 * @license http://www.digitaldeals.cz/license/
 */

namespace dlds\intercooler;

use yii\helpers\Html;
use yii\helpers\ArrayHelper;

/**
 * This is the main class of the Intercooler.js integration
 *
 * Provides easy to handle and maintain ajax request and replacing DOM content
 * with server response.
 *
 * @author Jiri Svoboda <jiri.svobodao@dlds.cz>
 * @package intercooler
 * @see http://intercoolerjs.org/docs.html
 *
 * Each intercooler ajax request has specific anatomy
 * @see http://intercoolerjs.org/docs.html#requests
 *
 * Intercooler provides bunch of event that can by listened for
 * * @see http://intercoolerjs.org/docs.html#events
 */
abstract class Intercooler extends \yii\base\Widget {

    /*
     * Requests types
     */
    const RQ_TYPE_GET = 'get-from';
    const RQ_TYPE_POST = 'post-to';
    const RQ_TYPE_PUT = 'put-to';
    const RQ_TYPE_PATCH = 'patch-to';
    const RQ_TYPE_DELETE = 'delete-from';
    const RQ_TYPE_SRC = 'src';

    /**
     * Basic trigger events
     */
    const EVENT_ON_READY = 'ready';
    const EVENT_ON_LOAD = 'load';
    const EVENT_ON_SCROLLED_INTO_VIEW = 'scrolled-into-view';

    /**
     * Intercooler attributes
     */
    const ATTR_PREFIX = 'ic';
    const ATTR_TRIGGER_ON = 'trigger-on';
    const ATTR_TRIGGER_DELAY = 'trigger-delay';
    const ATTR_TARGET = 'target';
    const ATTR_INCLUDE = 'include';
    const ATTR_INDICATOR = 'indicator';
    const ATTR_DEPENDS = 'deps';

    /**
     * @var mixed destination url as string or destination route as array
     * @see http://intercoolerjs.org/docs.html#core_attributes
     */
    public $url;

    /**
     * @var string request type
     * @see http://intercoolerjs.org/docs.html#core_attributes
     */
    public $type = self::RQ_TYPE_GET;

    /**
     * @var string event trigger, if equals false event will be autodetected
     * @see http://intercoolerjs.org/docs.html#triggers
     */
    public $when;

    /**
     * @var int delay till event is triggered
     * @see http://intercoolerjs.org/docs.html#triggers
     */
    public $delay;

    /**
     * @var string target element selector where responded content will be placed
     * @see http://intercoolerjs.org/docs.html#targeting
     */
    public $target;

    /**
     * @var string element selector which will be serialized and included into request
     * @see http://intercoolerjs.org/docs.html#inputs
     */
    public $include;

    /**
     * @var string selector of element indicating loading is in progress or
     * html content that will be used as indicator
     * @see http://intercoolerjs.org/docs.html#progress
     */
    public $indicator;

    /**
     * @var array depending urls or routes
     * @see http://intercoolerjs.org/docs.html#dependencies
     */
    public $depends;

    /**
     * @var string wrapper tag
     */
    public $tag = 'div';

    /**
     * @var array wrapper html options
     */
    public $options = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (!$this->url)
        {
            throw new \yii\base\InvalidConfigException('Url parameter must be specified.');
        }

        if (is_array($this->url))
        {
            $this->url = \yii\helpers\Url::to($this->url);
        }

        if (!is_array($this->options))
        {
            throw new \yii\base\InvalidConfigException('Options parameter must be an array.');
        }

        $this->registerClientScript();

        $options = $this->getWrapperOptions();

        echo Html::beginTag($this->tag, $options);
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        parent::run();

        echo Html::endTag($this->tag);
    }

    /**
     * Registers the needed JavaScript.
     */
    public function registerClientScript()
    {
        IntercoolerAsset::register($this->getView());
    }

    /**
     * Retrieves wrapper options based on widget config
     * @return array
     */
    protected function getWrapperOptions()
    {
        $options = ArrayHelper::merge($this->options, [
                self::getAttrName($this->type) => $this->url,
        ]);

        foreach ($this->getAttrBound() as $param => $attr)
        {
            if (isset($this->$param) && $this->$param)
            {
                $options[self::getAttrName($attr)] = $this->$param;
            }
        }

        return $options;
    }

    /**
     * Retrieves attributes name bound with widget params
     * @return array
     */
    protected function getAttrBound()
    {
        return [
            'when' => self::ATTR_TRIGGER_ON,
            'delay' => self::ATTR_TRIGGER_DELAY,
            'target' => self::ATTR_TARGET,
            'include' => self::ATTR_INCLUDE,
            'indicator' => self::ATTR_INDICATOR,
            'depends' => self::ATTR_DEPENDS,
        ];
    }

    /**
     * Retrieves intercooler attribute name
     * @param string $type
     * @return string attr name
     */
    public static function getAttrName($type)
    {
        return sprintf('%s-%s', self::ATTR_PREFIX, $type);
    }
}