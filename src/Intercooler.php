<?php

/**
 * @link http://www.digitaldeals.cz/
 * @copyright Copyright (c) 2016 Digital Deals s.r.o.
 * @license http://www.digitaldeals.cz/license/
 * @author Jiri Svoboda <jiri.svoboda@dlds.cz>
 */

namespace dlds\intercooler;

use yii\helpers\Url;
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
class Intercooler extends \yii\base\Object
{

    /**
     * X Headers
     */
    const XH_REQUEST = 'X-IC-Request';
    const XH_HTTP_METHOD_OVERRIDE = 'X-HTTP-Method-Override';
    const XH_TRIEGGER = 'X-IC-Trigger';
    const XH_REFRESH = 'X-IC-Refresh';
    const XH_REDIRECT = 'X-IC-Redirect';
    const XH_SCRIPT = 'X-IC-Script';
    const XH_CANCEL_POLLING = 'X-IC-CancelPolling';
    const XH_RESUME_POLLING = 'X-IC-ResumePolling';
    const XH_SET_POLL_INTERVAL = 'X-IC-SetPollInterval';
    const XH_OPEN = 'X-IC-Open';
    const XH_PUSH_URL = 'X-IC-PushURL';
    const XH_REMOVE = 'X-IC-Remove';

    /**
     * Prefix
     */
    const PREFIX_DEFAULT = 'ic';

    /**
     * Query params
     */
    const QP_TARGET = 'target-id';
    const QP_TRIGGER = 'trigger-id';
    const QP_LAST_REFRESH = 'last-refresh';
    /*
     * Requests types
     */
    const RQ_TYPE_GET = 'get-from';
    const RQ_TYPE_POST = 'post-to';
    const RQ_TYPE_PUT = 'put-to';
    const RQ_TYPE_PATCH = 'patch-to';
    const RQ_TYPE_DELETE = 'delete-from';
    const RQ_TYPE_APPEND = 'append-from';
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
    const ATTR_ACTION = 'action';
    const ATTR_ADD_CLASS = 'add-class';
    const ATTR_CONFIRM = 'confirm';
    const ATTR_DEPENDS = 'deps';
    const ATTR_INDICATOR = 'indicator';
    const ATTR_LIMIT_CHILDREN = 'limit-children';
    const ATTR_LOCAL_VARS = 'local-vars';
    const ATTR_POST_ERRORS_TO = 'post-errors-to';
    const ATTR_PREPEND_FROM = 'prepend-from';
    const ATTR_PROMPT = 'prompt';
    const ATTR_PUSH_URL = 'push-url';
    const ATTR_SELECT_FROM_RESPONSE = 'select-from-response';
    const ATTR_STYLE_SRC = 'style-src';
    const ATTR_TRANSITION_DURATION = 'transition-duration';
    const ATTR_VERB = 'verb';
    // includes
    const ATTR_GLOBAL_INCLUDE = 'global-include';
    const ATTR_INCLUDE = 'include';
    // poll
    const ATTR_PAUSE_POLLING = 'pause-polling';
    const ATTR_POLL = 'poll';
    const ATTR_POLL_REPEATS = 'poll-repeats';
    // remove | replace
    const ATTR_REMOVE_AFTER = 'remove-after';
    const ATTR_REMOVE_CLASS = 'remove-class';
    const ATTR_REPLACE_TARGET = 'replace-target';
    // scroll
    const ATTR_SCROLL_OFFSET = 'scroll-offset';
    const ATTR_SCROLL_TO_TARGET = 'scroll-to-target';
    // triggers & targets
    const ATTR_TRIGGER_ON = 'trigger-on';
    const ATTR_TRIGGER_DELAY = 'trigger-delay';
    const ATTR_TARGET = 'target';
    // events
    const ATTR_EVT_ON_BEFORE_SEND = 'on-beforeSend';
    const ATTR_EVT_ON_BEFORE_TRIGGER = 'on-beforeTrigger';
    const ATTR_EVT_ON_COMPLETE = 'on-complete';
    const ATTR_EVT_ON_ERROR = 'on-error';
    const ATTR_EVT_ON_SUCCESS = 'on-success';

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
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->registerClientScript();

        if (is_array($this->url)) {
            $this->url = \yii\helpers\Url::to($this->url);
        }
    }

    /**
     * Retrieves wrapper options based on widget config
     * @return array
     */
    public function getOptions($id, array $additionals = [])
    {
        $options = [
            'id' => $id,
            static::attr($this->type) => $this->url,
        ];

        foreach ($this->getAttrBound() as $param => $attr) {
            if (isset($this->$param) && $this->$param) {
                $options[static::attr($attr)] = $this->$param;
            }
        }

        return ArrayHelper::merge($options, $additionals);
    }

    /**
     * Registers the needed JavaScript.
     */
    public function registerClientScript()
    {
        IntercoolerAsset::register(\Yii::$app->getView());
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
     * @param string $key
     * @return string attr name
     */
    public static function attr($key)
    {
        return sprintf('%s-%s', self::PREFIX_DEFAULT, $key);
    }

    /**
     * Sets redirect headers for given url
     * @param string $url
     */
    public static function doRedirect($url)
    {
        if (is_array($url)) {
            $url = Url::to($url);
        }

        $headers = ($response = \Yii::$app->getResponse()) ? $response->headers : false;

        if ($headers) {
            $headers->add(self::XH_REDIRECT, $url);
        }
    }

    /**
     * Sets refresh headers for given paths
     * @param array $paths
     */
    public static function doRefresh(array $paths = [])
    {
        $headers = ($response = \Yii::$app->getResponse()) ? $response->headers : false;

        if ($headers) {
            $headers->add(self::XH_REFRESH, implode(',', $paths));
        }
    }

    /**
     * Sets remove headers
     * @param boolean $value
     */
    public static function doRemove($value = true)
    {
        $headers = ($response = \Yii::$app->getResponse()) ? $response->headers : false;

        if ($headers) {
            $headers->add(self::XH_REMOVE, $value);
        }
    }

}
