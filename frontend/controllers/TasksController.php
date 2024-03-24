<?php

namespace frontend\controllers;
use Yii;
use yii\rest\ActiveController;
use yii\helpers\ArrayHelper;
use common\models\Tasks;
use common\models\Users;
use common\models\Sid;

include '../functions/generateRandomString.php';

class TasksController extends ActiveController
{
    public $modelClass = 'common\models\Tasks';

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
        unset($actions['index']);
        return $actions;
    }

    // post: 'domain/tasks' Создание задачи
    public function actionCreate()
    {
        try{
            
            // Достаем данные из запроса
            $title = Yii::$app->request->post('title');
            $text = Yii::$app->request->post('text');
            $sid = Yii::$app->request->post('sid');

            // Запросы к БД
            $id_user = Sid::findOne(['sid'=> $sid])->id_user;

            // Генерируем токен
            $randomString = generateRandomString(7, '#');

            // Создаем задачу
            $task = new Tasks();
            $task->token = $randomString;
            $task->title = $title;
            $task->text = $text;
            $task->id_user_creator = $id_user;
            $task->save();

            return true;

        }catch(\Exception $e){
            return ['errorText' => $e->getMessage()];
        }
    }

    // post: 'domain/tasks/my-tasks' Чтение задач пользователя 
    public function actionMyTasks($id)
    {
        try {
            
            return $id;

        } catch (\Exception $e) {
            return ['errorText' => $e->getMessage()];
        }
    }
}
