<?php

/**
 * @copyright Copyright 2016  &copy; Digital Deals s.r.o.
 * @license http://www.digitaldeals.cz/license/
 * @author Jiri Svoboda <jiri.svoboda@dlds.cz>
 */

namespace frontend\components\helpers\url\rules;

/**
 * This is frontend URL RULE helper for table "my_advanced".
 *
 * Rule helper is always joined with appropriate route helper in this case \frontend\components\helpers\url\routes\MyBasicRouteHelper.
 * For understanding url rule helper basics @see https://github.com/dlds/yii2-giixer/blob/master/docs/classes/MyBasicUrlRuleHelper.php
 *
 * In this helper we are setting dynamic parsing including model custom slugs. Our 'MyAdvanced' AR models have their own slugs defined in DB.
 * For getting these slugs it usually used class property called 'slug' ($model->slug).
 * We want to set 'view' rule to be able to parse urls like 'http://www.mydomain.com/my-custom-model-slug/'
 * where 'my-custom-model-slug' is stored in $model->slug property
 *
 * @see \dlds\giixer\components\helpers\GxUrlRuleHelper
 * @see http://www.yiiframework.com/doc-2.0/guide-runtime-routing.html
 */
class MyAdvancedUrlRuleHelper extends \dlds\giixer\components\helpers\GxUrlRuleHelper {

    /**
     * Valid url patterns
     * ------------------
     * Each URL rule consists of a pattern used for matching the path info part of URLs
     * @see http://www.yiiframework.com/doc-2.0/guide-runtime-routing.html#url-rules
     * @see http://www.yiiframework.com/doc-2.0/guide-runtime-routing.html#named-parameters
     */
    const PATTERN_VIEW = '<slug:[-\w]+>';

    /**
     * Retrieves view rule
     * --------------------
     * @return string rule
     */
    public static function view() {
        // gets appropriate route
        $route = \frontend\components\helpers\url\routes\MyBasicRouteHelper::ROUTE_VIEW;

        // sets dynamic pattern
        $pattern = self::PATTERN_VIEW;

        // retriever final rule
        return self::getRule($pattern, $route);
    }

    /**
     * Overriden method where we are checking if current route is 'view' route.
     * If it is we use custom 'createUrl' method to be able to use custom slugs
     * @see http://www.yiiframework.com/doc-2.0/yii-web-urlmanager.html#createUrl()-detail
     * @return final url
     */
    public function createUrl($manager, $route, $params) {
        if ($route === \frontend\components\helpers\url\routes\MyBasicRouteHelper::ROUTE_VIEW) {
            return $this->createViewUrl($manager, $route, $params);
        }

        return parent::createUrl($manager, $route, $params);
    }

    /**
     * Overriden method where we are checking if current requested route is 'view' route.
     * If it is we use custom 'parseRequest' method to be able to parse custom slugs
     * @see http://www.yiiframework.com/doc-2.0/yii-web-urlmanager.html#parseRequest()-detail
     * @return matched route
     */
    public function parseRequest($manager, $request) {
        if ($this->route === \frontend\components\helpers\url\routes\MyBasicRouteHelper::ROUTE_VIEW) {
            return $this->parseViewRequest($manager, $request);
        }

        return parent::parseRequest($manager, $request);
    }

    /**
     * Creates custom view url with ability to pull custom slug of
     * appropriate AR model from DB and use it in final url
     */
    protected function createViewUrl($manager, $route, $params) {
        // routes are always based on AR models primary keys so to avoid showing
        // this primary key in url we have to remove it from route params
        $id = \yii\helpers\ArrayHelper::remove($params, 'id');

        // if there is no 'id' in route this rule will not match
        if ($id) {
            // finds appropriate AR model based on its primary key in current route
            $model = \frontend\models\db\MyBasic::findOne($id);

            // if there is no AR with given primary key this rule will not match
            if ($model) {
                // we have to push custom AR model slug to current route
                return parent::createUrl($manager, $route, \yii\helpers\ArrayHelper::merge($params, [
                                    'slug' => $model->slug,
                ]));
            }
        }

        return false;
    }

    /**
     * Parses current view request with ability to match custom slugs
     * appropriate AR model
     */
    protected function parseViewRequest($manager, $request) {
        // gets all request params
        $params = ArrayHelper::getValue(parent::parseRequest($manager, $request), 1);

        // if there is no params in current request this rule will not match
        if ($params) {
            // finds model by current slug
            $model = \frontend\models\db\MyBasic::find()->andWhere([
                        \frontend\models\db\MyBasic::tablename() . '.slug' => ArrayHelper::getValue($params, 'post')
                    ])->one();

            // if model is not found this rule will not match
            if (!$model) {
                return false;
            }

            // get appropriate route
            $route = \frontend\components\helpers\url\routes\MyBasicRouteHelper::view($model);

            // gets default route name & params
            $name = \frontend\components\helpers\url\routes\MyBasicRouteHelper::getName($route);
            $params = \frontend\components\helpers\url\routes\MyBasicRouteHelper::getParams($route);

            // retrieves mathced route
            return [$name, $params];
        }

        return false;
    }

}
