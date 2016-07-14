<?php

/**
 * @copyright Copyright 2016  &copy; Digital Deals s.r.o.
 * @license http://www.digitaldeals.cz/license/
 * @author Jiri Svoboda <jiri.svoboda@dlds.cz>
 */

namespace frontend\components\helpers\url\routes;

use yii\helpers\ArrayHelper;

/**
 * This is frontend ROUTE helper for table "my_basic".
 *
 * Route helper is always joined with appropriate controller in this case \frontend\controllers\MyBasicController
 * and appropriate AR model in this case \frontend\models\db\MyBasic
 *
 * @see \dlds\giixer\components\helpers\GxRouteHelper
 * @see http://www.yiiframework.com/doc-2.0/guide-runtime-routing.html
 */
class MyBasicRouteHelper extends \dlds\giixer\components\helpers\GxRouteHelper {

    /**
     * Valid routes
     * ------------
     * Common structure: controller-name/controller-action.
     * Routes for all controller actions should be listed below.
     */
    const ROUTE_INDEX = 'my-basic/index';
    // ...
    const ROUTE_VIEW = 'my-basic/view';

    // ...

    /**
     * Retrieves index route
     * ---------------------
     * Usually used like Url::to(MyBasicRouteHelper::index()) to get final url
     * @see http://www.yiiframework.com/doc-2.0/guide-runtime-routing.html#creating-urls
     * @param array $params additional route params
     * @return array route
     */
    public static function index(array $params = []) {
        // adds foremost slash to route name for correct parsing
        $route = sprintf('/%s', self::ROUTE_INDEX);

        // merges given route name and params and retrieves standart route array
        return self::getRoute($route, $params);
    }

    /**
     * Retrieves view route
     * ----------------------
     * Usually used like Url::to(MyBasicRouteHelper::view($instance)) to get final url
     * where instance is \frontend\models\db\MyBasic model instance reference
     * @param \frontend\models\db\MyBasic $model given AR model
     * @param array $params additional route params
     * @return array route
     */
    public static function view(\frontend\models\db\MyBasic $model, array $params = []) {
        // adds foremost slash to route name for correct parsing
        $route = sprintf('/%s', self::ROUTE_VIEW);

        // primary key of given AR is pushed as param named 'id' to current route automatically
        return self::getRoute($route, ArrayHelper::merge(['id' => $model->primaryKey], $params));
    }

    // ...
}
