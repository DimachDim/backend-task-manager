<?php

namespace frontend\controllers;

use yii\rest\ActiveController;
use Yii;
use yii\helpers\ArrayHelper;
include '../functions/generateRandomString.php';

// models
use common\models\Users;
use common\models\Sid;
use common\models\InvitationTokens;


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

    // post: 'domain/users' Регистрация пользователя
    public function actionCreate()
    {
        try{

            // Создаем экземпляры моделей
            $user = new Users;
            $sid = new Sid;
            
            // Извлекаем данные из запроса
            $userName = Yii::$app->request->post('userName');
            $password = Yii::$app->request->post('password');
            $token = Yii::$app->request->post('token');

            // Ищем данные в базе
            $tokenInBD = InvitationTokens::findOne(['token'=>$token]);  // Запись по токену
            $userNameInBD = Users::findOne(['username'=>$userName]);    // Запись по имени пользователя

            // Если такой токен есть
            if($tokenInBD)
            {        
                // Если по такому токену не заходили
                if($tokenInBD->id_invited_user == null)
                {
                    
                    // Если такое имя пользователя не занято
                    if($userNameInBD == null)
                    {
                        //Создаем пользователя
                        $user->username = $userName;
                        $user->password = $password;
                        $user->save();

                        //Указываем что токен занят
                        $tokenInBD->id_invited_user = $user->id;
                        $tokenInBD->save();

                        //Создаем сессию
                        $randomString = generateRandomString(rand(9,20));
                        $sid->sid = $randomString;
                        $sid->id_user = Users::findOne(['username'=>$userName])->id;
                        $sid->save();

                        //Возвражаем данные
                        return ['sid' => $sid->sid];

                    }else
                    {
                        throw new \Exception('Такое имя пользователя уже занято.');
                    }
                    
                // Если по такому токену уже заходили
                }else{
                    throw new \Exception('По такому токену уже приглашали другого пользователя'); 
                }

            // Токена нет
            }else{
                throw new \Exception('По такому токену нет приглашений');
            }
                    
        }catch(\Exception $e){
            return ['errorText' => $e->getMessage()];
        }
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