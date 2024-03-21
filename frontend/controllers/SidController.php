<?php

namespace frontend\controllers;
use Yii;
use yii\rest\ActiveController;
use yii\helpers\ArrayHelper;
// models
use common\models\Sid;
use common\models\Users;

include '../functions/generateRandomString.php';

class SidController extends ActiveController
{
    public $modelClass = 'common\models\Sid';

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
        return $actions;
    }

    // post: 'domain/sids' Создание сесси
    public function actionCreate()
    {
        try{
            
            // Вытаскиваем данные запроса
            $userName = Yii::$app->request->post('userName');
            $password = Yii::$app->request->post('password');

            // Запросы к БД
            $user = Users::findOne(['username' => $userName]);

            // Если есть такой пользователь
            if($user != null)
            {
                // Если пароль подходит
                if($user->password == $password)
                {
                    // Создаем сессию
                    $sid = new Sid();
                    $randomString = generateRandomString(rand(9,20));
                    $sid->sid = $randomString;
                    $sid->id_user = $user->id;
                    $sid->save();

                    // Отправляем сессию пользователю
                    return ['sid' => $sid->sid];
                
                // Если пароль не подходит
                }else{
                    throw new \Exception('Неверный пароль.');
                }
            
            // Нет такого пользователя
            }else{
                throw new \Exception('Такой пользователь не зарегистрирован.');
            }

        }catch(\Exception $e){
            return ['errorText' => $e->getMessage()];
        }
    }

}
