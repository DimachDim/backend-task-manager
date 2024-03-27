<?php

namespace frontend\controllers;
use Yii;
use yii\rest\ActiveController;
use yii\helpers\ArrayHelper;
use common\models\Tasks;
use common\models\Users;
use common\models\Sid;
use common\models\TaskStatuses;


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
        unset($actions['my-tasks']);
        unset($actions['pending-tasks']);
        unset($actions['delete']);
        unset($actions['create']);
        unset($actions['index']);
        unset($actions['view']);
        return $actions;
    }

    // post: 'domain/tasks' Создание задачи
    public function actionCreate()
    {
        try{
            
            // Достаем данные из запроса
            $title = Yii::$app->request->post('title');                 // заголовок
            $text = Yii::$app->request->post('text');                   // текст
            $id_user_executor = Yii::$app->request->post('executorId'); // Исполнитель
            $date_start = Yii::$app->request->post('startDate');        // дата начала
            $date_end = Yii::$app->request->post('endDate');            // дата конца
            $sid = Yii::$app->request->post('sid');                     // сессия

            // Запросы к БД
            $id_user = Sid::findOne(['sid'=> $sid])->id_user;           // id создателя задачи

            // Генерируем токен
            $randomString = generateRandomString(7, '#');

            // Создаем задачу
            $task = new Tasks();
            $task->token = $randomString;                   // токен
            $task->title = $title;                          // заголовок
            $task->text = $text;                            // текст
            $task->id_user_creator = $id_user;              // создатель задачи
            $task->id_user_executor = $id_user_executor;    // исполнитель задачи
            $task->date_start = $date_start;                // дата начала
            $task->date_end = $date_end;                    // дата конца
            $task->id_status = 3;                           // статус "Не начато"
            $task->save();

            return true;

        }catch(\Exception $e){
            return ['errorText' => $e->getMessage()];
        }
    }

    // get: 'domain/tasks/my-tasks/<id>' Чтение задач созданных пользователем
    public function actionMyTasks($id)
    {
        try {
            // Запрашиваем задачи которые создал пользователь
            $arrTasks = Tasks::findAll(['id_user_creator'=>$id]);
            $newArrTasks = [];      // Для копирования массива

            // Перебираем полученный массив
            foreach ($arrTasks as $task) {
                // Конвертируем объект в массив
                $newTask = $task->attributes;
                // Добавляем новое свойство. Запрашиваем имя пользователя в базе
                $newTask['userName'] = Users::findOne(['id' => $task->id_user_creator])->username;
                // Добавляем измененный task в новый массив
                $newArrTasks[] = $newTask;
            }

            return $newArrTasks;

        } catch (\Exception $e) {
            return ['errorText' => $e->getMessage()];
        }
    }

    // get: 'domain/tasks/pending-tasks/<id>' Чтение задач назначеных пользователю
    public function actionPendingTasks($id)
    {
        try {
            // Запрашиваем задачи которые назначены нам
            $arrTasks = Tasks::findAll(['id_user_executor'=>$id]);
            $newArrTasks = [];      // Для копирования массива

            // Перебираем полученный массив
            foreach ($arrTasks as $task) {
                // Конвертируем объект в массив
                $newTask = $task->attributes;
                // Добавляем новое свойство. Запрашиваем имя пользователя в базе
                $newTask['userName'] = Users::findOne(['id' => $task->id_user_creator])->username;
                // Добавляем измененный task в новый массив
                $newArrTasks[] = $newTask;
            }

            return $newArrTasks;

        } catch (\Exception $e) {
            return ['errorText' => $e->getMessage()];
        }
    }

    // get: 'domain/tasks/<id>' Чтение одной задачи 
    public function actionView($id)
    {
        try {
            // Ищем задачу в базе по id
            $task = Tasks::findOne(['id'=>$id]);   
            // Копируем запись для добавления новых данных
            $newData = (array) $task->attributes;
            // Добавляем новое значение
            $newData['userNameCreator'] = Users::findOne(['id'=> $task->id_user_creator])->username;
            $newData['userNameExecutor'] = Users::findOne(['id'=> $task->id_user_executor])->username;
            $newData['statusName'] = TaskStatuses::findOne(['id'=> $task->id_status])->statusName;

            return $newData;

        } catch (\Exception $e) {
            return ['errorText' => $e->getMessage()];
        }
    }

    // delete: 'domain/tasks/<id>' Удаление задачи
    public function actionDelete($id)
    {
        try {
            
            // Поиск в базе
            $task = Tasks::findOne(['id'=> $id]);
            $task->delete();

            //return 'ok';

        } catch (\Exception $e) {
            return ['errorText' => $e->getMessage()];
        }
    }
}
