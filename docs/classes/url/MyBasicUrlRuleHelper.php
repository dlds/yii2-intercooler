<?php

/**
 * @copyright Copyright 2016  &copy; Digital Deals s.r.o.
 * @license http://www.digitaldeals.cz/license/
 * @author Jiri Svoboda <jiri.svoboda@dlds.cz>
 */

namespace frontend\components\helpers\url\rules;

/**
 * This is frontend URL RULE helper for table "my_basic".
 *
 * Rule helper is always joined with appropriate route helper in this case \frontend\components\helpers\url\routes\MyBasicRouteHelper
 *
 * @see \dlds\giixer\components\helpers\GxUrlRuleHelper
 * @see http://www.yiiframework.com/doc-2.0/guide-runtime-routing.html
 */
class MyBasicUrlRuleHelper extends \dlds\giixer\components\helpers\GxUrlRuleHelper {

    /**
     * Valid url patterns
     * ------------------
     * Each URL rule consists of a pattern used for matching the path info part of URLs
     * @see http://www.yiiframework.com/doc-2.0/guide-runtime-routing.html#url-rules
     * @see http://www.yiiframework.com/doc-2.0/guide-runtime-routing.html#named-parameters
     */
    const PATTERN_INDEX = 'my-basic-index';
    const PATTERN_VIEW = 'my-basic-view';
    // ...
    const PATTERN_ACTION = '<id:\d+>-%s';

    // ...

    /**
     * Retrieves index rule
     * --------------------
     * Automatically pull appropriate route from \frontend\components\helpers\url\routes\MyBasicRouteHelper
     * Works with pretty url. Pretty slugs are defined in common translation files (in this case 'frontend/messages/url/mybasic')
     * so it is easy to have multilangual routing system
     * @return string rule
     */
    public static function index() {
        // gets appropriate route for this rule
        $route = \frontend\components\helpers\url\routes\MyBasicRouteHelper::ROUTE_INDEX;

        // gets pretty url slug for current pattern from translation file
        $pattern = \Yii::t('url/mybasic', self::PATTERN_INDEX);

        // retrieves final rule - it is possible to define host, verb and mode as additional parameters
        return self::getRule($pattern, $route);
    }

    /**
     * Retrieves view rule
     * --------------------
     * Automatically pull appropriate route from \frontend\components\helpers\url\routes\MyBasicRouteHelper
     * Works with pretty url. Pretty slugs are defined in common translation files (in this case 'frontend/messages/url/mybasic')
     * so it is easy to have multilangual routing system
     * @return string rule
     */
    public static function view() {
        // gets appropriate route for this rule
        $route = \frontend\modules\tools\components\helpers\url\routes\ToolsCompassValueRouteHelper::ROUTE_VIEW;

        // gets pretty url slug for current pattern from translation file
        // to be able to ensure slug uniqueness for multiple different view pages this slug is defined with parameter named 'id'
        // using action pattern defined above the final url will look like '23-show-my-basic' or '12-show-my-basic'
        $pattern = sprintf(self::PATTERN_ACTION, \Yii::t('url/mybasic', self::PATTERN_VIEW));

        // retrieves final rule - it is possible to define host, verb and mode as additional parameters
        return self::getRule($pattern, $route);
    }

}
