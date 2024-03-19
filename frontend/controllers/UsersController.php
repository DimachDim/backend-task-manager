<?php

namespace frontend\controllers;

use yii\rest\ActiveController;
use Yii;
use common\models\Users;


class UsersController extends ActiveController
{
    public $modelClass = 'common\models\Users';

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