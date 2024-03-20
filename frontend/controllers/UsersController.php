<?php

namespace frontend\controllers;

use yii\rest\ActiveController;
use Yii;
use common\models\Users;
use yii\helpers\ArrayHelper;



class UsersController extends ActiveController
{
    public $modelClass = 'common\models\Users';
    

    //cors
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'corsFilter' => [
                'class' => \yii\filters\Cors::className(),
                'cors'  => [
                    'Origin'                           => ['*'],
                    'Access-Control-Request-Method'    => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                    'Access-Control-Allow-Credentials' => false,
                    'Access-Control-Max-Age'           => 3600,
                    'Access-Control-Expose-Headers'    => ['X-Pagination-Current-Page'],
                ],
            ],
        ]);
    }
    //end cors

    public function actions()
    {
        $actions = parent::actions();
        // Указываем какие экшены будут
        unset($actions['create']);
        unset($actions['update']);   
        unset($actions['delete']);   
        return $actions;
    }

    // post: 'domain/users'
    public function actionCreate()
    {
        // Создаем экземпляр модели
        $model = new Users();
        // Вытаскиваем данные из запроса и присваеваем модели
        $model->username = Yii::$app->request->post('userName');
        // Сохраняем изменения
        $model->save();
        // Возвращаем модель пользователю
        return $model;
    }

    // patch: 'domain/users/id
    public function actionUpdate($id)
    {
        // Ищем запись в БД по id
        $model = Users::findOne($id);
        // Меняем значение на значение из запроса
        $model->username = Yii::$app->request->post('username');
        // Сохраняем изменения
        $model->save();
        // Возвращаем клиенту
        return $model;
    }

    // delete: 'domain/users/id
    public function actionDelete($id)
    {
        $model = Users::findOne($id);
        $model->delete();
    }
}