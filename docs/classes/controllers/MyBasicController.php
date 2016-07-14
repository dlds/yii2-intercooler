<?php

namespace backend\controllers;

use yii\filters\VerbFilter;
use dlds\giixer\components\helpers\GxFlashHelper;
use common\components\helpers\images\MyBasicImageHelper;
use backend\components\handlers\crud\MyBasicCrudHandler;
use backend\components\handlers\search\MyBasicSearchHandler;
use backend\components\helpers\url\routes\MyBasicUrlRouteHelper;

/**
 * MyBasicController implements the CRUD actions for MyBasic model.
 */
class MyBasicController extends \yii\web\Controller
{
    // ...

    /**
     * Lists all MyBasic models.
     * ---
     * Uses MyBasicSearchHandler to process query params, 
     * handle data provider etc...
     * ---
     * @return mixed
     */
    public function actionIndex()
    {
        $handler = new MyBasicSearchHandler(\Yii::$app->request->queryParams);

        return $this->render('index', [
                    'searchHandler' => $handler,
        ]);
    }

    /**
     * Displays a single MyBasic model.
     * ---
     * Uses MyBasicCrudHandler which handles Read action and retrieves
     * event with all information about result of reading
     * ---
     * @param integer $id primary key
     * @return mixed
     */
    public function actionView($id)
    {
        $handler = new MyBasicCrudHandler();

        // process reading
        $evt = $handler->read($id);

        // if reading was not successfull call not found callback
        if (!$evt->isRead()) {
            return $handler->notFoundFallback();
        }

        // if model was found render view
        return $this->render('view', [
                    'model' => $evt->model,
        ]);
    }

    /**
     * Creates a new MyBasic model.
     * ---
     * Uses MyBasicCrudHandler which handles Read action and retrieves
     * event with all information about result of reading
     * ---
     * Shows easy attaching of custom action on specific CRUD event
     * ---
     * @return mixed
     */
    public function actionCreate()
    {
        $handler = new MyBasicCrudHandler();

        $handler->on(MyBasicCrudHandler::EVENT_BEFORE_LOAD, function(GxCrudEvent $e) {
            
            // custom logic which will be processed before model is loaded with request data
            // requested data can be accessed by $e->input
        });
        
        // process creating
        $evt = $handler->create(\Yii::$app->request->post());

        // if creating was succesfull set Flash message and redirect to index
        if ($evt->isCreated()) {
            GxFlashHelper::setFlash(GxFlashHelper::FLASH_SUCCESS, GxFlashHelper::message(GxFlashHelper::MESSAGE_CREATE_SUCCESS));

            return $this->redirect(MyBasicUrlRouteHelper::index());
        }

        // render create view
        return $this->render('create', [
                    'model' => $evt->model,
        ]);
    }

    /**
     * Updates an existing MyBasic model.
     * @param integer $id primary key
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $handler = new MyBasicCrudHandler();

        // process updating
        $evt = $handler->update($id, \Yii::$app->request->post());

        // if updating was successfull set Flash message
        if ($evt->isUpdated()) {
            GxFlashHelper::setFlash(GxFlashHelper::FLASH_SUCCESS, GxFlashHelper::message(GxFlashHelper::MESSAGE_UPDATE_SUCCESS));
        }

        // render update view
        return $this->render('update', [
                    'model' => $evt->model,
        ]);
    }

    /**
     * Deletes an existing MyBasic model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id primary key
     * @return mixed
     */
    public function actionDelete($id)
    {
        $handler = new MyBasicCrudHandler();

        $handler->on(MyBasicCrudHandler::EVENT_AFTER_DELETE, function(GxCrudEvent $e) {
            
            // custom logic after deletion
        });
        
        // process delete
        $evt = $handler->delete($id);

        // if deletion was succesfull set flash message and redirect to index
        if ($evt->isDeleted()) {
            GxFlashHelper::setFlash(GxFlashHelper::FLASH_SUCCESS, GxFlashHelper::message(GxFlashHelper::MESSAGE_DELETE_SUCCESS));

            return $this->redirect(MyBasicUrlRouteHelper::index());
        }

        // if delete failed set flash message and redirect to not processabel callback
        GxFlashHelper::setFlash(GxFlashHelper::FLASH_ERROR, GxFlashHelper::message(GxFlashHelper::MESSAGE_DELETE_FAIL));

        return $handler->notProcessableFallback();
    }

}
