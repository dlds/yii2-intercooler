<?php
/**
 * @link http://www.digitaldeals.cz/
 * @copyright Copyright (c) 2016 Digital Deals s.r.o.
 * @license http://www.digitaldeals.cz/license/
 */

namespace dlds\lazyload;

/**
 * This is the main class of the LazyPjax widget
 *
 * @author Jiri Svoboda <jiri.svobodao@dlds.cz>
 * @package lazyload
 */
class LazyPjax extends \yii\widgets\Pjax {

    /**
     * @var array pjax options
     */
    public $pjaxOptions;

    /**
     * @var array lazy options
     */
    public $lazyOptions;

    /**
     * @inheritdoc
     */
    public function init()
    {
        die('ee');
        if (!isset($this->options['id']))
        {
            $this->options['id'] = $this->getId();
        }

        if ($this->requiresPjax())
        {
            ob_start();
            ob_implicit_flush(false);
            $view = $this->getView();
            $view->clear();
            $view->beginPage();
            $view->head();
            $view->beginBody();
            if ($view->title !== null)
            {
                echo Html::tag('title', Html::encode($view->title));
            }
        }
        else
        {
            $options = $this->options;
            $tag = ArrayHelper::remove($options, 'tag', 'div');
            echo Html::beginTag($tag, array_merge([
                'data-pjax-container' => '',
                'data-pjax-push-state' => $this->enablePushState,
                'data-pjax-replace-state' => $this->enableReplaceState,
                'data-pjax-timeout' => $this->timeout,
                'data-pjax-scrollto' => $this->scrollTo,
                    ], $options));
        }
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        
    }

    /**
     * Registers the needed JavaScript.
     */
    public function registerClientScript()
    {
        $id = $this->options['id'];
        $this->clientOptions['push'] = $this->enablePushState;
        $this->clientOptions['replace'] = $this->enableReplaceState;
        $this->clientOptions['timeout'] = $this->timeout;
        $this->clientOptions['scrollTo'] = $this->scrollTo;
        $options = Json::htmlEncode($this->clientOptions);
        $js = '';
        if ($this->linkSelector !== false)
        {
            $linkSelector = Json::htmlEncode($this->linkSelector !== null ? $this->linkSelector : '#'.$id.' a');
            $js .= "jQuery(document).pjax($linkSelector, \"#$id\", $options);";
        }
        if ($this->formSelector !== false)
        {
            $formSelector = Json::htmlEncode($this->formSelector !== null ? $this->formSelector : '#'.$id.' form[data-pjax]');
            $js .= "\njQuery(document).on('submit', $formSelector, function (event) {jQuery.pjax.submit(event, '#$id', $options);});";
        }
        $view = $this->getView();
        PjaxAsset::register($view);

        if ($js !== '')
        {
            $view->registerJs($js);
        }
    }

    /**
     * @inheritdoc
     */
    public static function begin($config = [])
    {
        throw new \yii\base\NotSupportedException;
    }

    /**
     * @inheritdoc
     */
    public static function end()
    {
        throw new \yii\base\NotSupportedException;
    }

    /**
     * @inheritdoc
     */
    public static function widget($config = [])
    {
        parent::begin($config);
    }
}