<?php

namespace common\models\db\base;

use Yii;

/**
 * This is base model class for table "my_model".
 *
 * ... here comes property docs ...
 *
 */
abstract class MyModel extends \dlds\giixer\components\GxActiveRecord {

    /**
     * Relations names
     * ---------------
     * Each AR model relation has its own name stored in class constant
     * @see http://www.yiiframework.com/doc-2.0/guide-runtime-routing.html#url-rules
     * @see http://www.yiiframework.com/doc-2.0/guide-runtime-routing.html#named-parameters
     */
    const RN_MY_ANOTHER_MODEL = 'myAnotherModel';

    // ... standart AR methods (rules, behaviros, attrs labels, relations, ... )

    /**
     * Model Record print
     * ------------------
     * Method indicates which model attribute (or related attr) should be used
     * when model is printed as string.
     * This method is usually ovveriden in descendants
     */
    public function getRecordPrint() {
        return $this->lastname;
    }

    /**
     * Model Query assignment
     * ----------------------
     * Appropriate ActiveQuery is assigned to the model right in this base class
     */
    public static function find() {
        return new \app\modules\kernel\models\db\query\AppAuthorQuery(get_called_class());
    }

}
