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
 * This is the main class of the LazyPjax widget
 *
 * @author Jiri Svoboda <jiri.svobodao@dlds.cz>
 * @package lazyload
 */
class LazyAjax extends \yii\base\Widget {

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
        $url = ArrayHelper::getValue($this->lazyOptions, 'url', false);

        if (!$url)
        {
            throw new \yii\base\InvalidConfigException('lazyOptions.url must be specified');
        }

        // set pjax options
        \Yii::configure($this, $this->pjaxOptions);

        return parent::init();
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        $html = '';

        $loading = ArrayHelper::getValue($this->lazyOptions, 'loadingMsg', false);

        if ($loading)
        {
            $html .= Html::tag('div', $loading, ['lazypjax-loading-msg']);
        }

        $url = ArrayHelper::getValue($this->lazyOptions, 'url', false);

        if ($url)
        {
            //$html = Html::a('do it', $url, ['lazypjax-trigger']);
        }

        echo $html;

        return parent::run();
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
        return parent::widget($config);
    }

    /**
     * Registers the needed JavaScript.
     */
    public function registerClientScript()
    {
        $url = ArrayHelper::getValue($this->lazyOptions, 'url', false);


        if ($url)
        {
            if (is_array($url))
            {
                $url = \yii\helpers\Url::to($url);
            }

            $selector = sprintf('#%s', $this->id);

            $pjaxOptions = json_encode(ArrayHelper::merge($this->pjaxOptions, [
                    'url' => $url,
                    'container' => $selector,
                    //'async' => false,
                    'push' => false,
            ]));

            $js = "\njQuery(document).on('ready', function (event) {
                console.log('run $selector');
                jQuery.pjax($pjaxOptions);
            });";

            $fallbackMsg = ArrayHelper::getValue($this->lazyOptions, 'fallbackMsg', false);

            $js .= "\njQuery(document).on('pjax:error', '$selector', function (e) {
                jQuery(this).html('$fallbackMsg');
            });";

            $this->getView()->registerJs($js);
        }

        return parent::registerClientScript();
    }
}