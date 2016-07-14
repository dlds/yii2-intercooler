<?php

namespace common\models\db\query;

/**
 * This is common ActiveQuery class for [[\app\models\db\MyModel]].
 *
 * @see \app\models\db\MyModel
 */
class MyModelQuery extends \dlds\giixer\components\GxActiveQuery {

    /**
     * @inheritdoc
     * @return \app\models\db\MyModel
     */
    protected function modelClass() {
        return \app\models\db\MyModel::className();
    }

    /**
     * @inheritdoc
     * @return \app\models\db\MyModel
     */
    protected function modelTable() {
        return \app\models\db\MyModel::tableName();
    }

}
