<?php

namespace frontend\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\helpers\ArrayHelper;
include '../functions/generateRandomString.php';

// models
use common\models\Followers;
use common\models\Users;


class FollowersController extends ActiveController
{
    public $modelClass = 'common\models\Followers';
    

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
        unset($actions['yes-no']);
        unset($actions['get-my-subscriptions']);   
        unset($actions['get-my-followers']);
        unset($actions['delete']);   
        unset($actions['create']);  
        return $actions;
    }

   //get: 'domain/followers/yes-no/<userId>/<frendId> Получает текузего пользователя и второго. Сверяет подписаны они или нет
   public function actionYesNo($userId, $frendId)
   {
        try 
        {
            // Ищем все записи где текущий пользователь подписан на запрашеваемого
            $followers = Followers::find()->where(['id_user'=> $userId, 'id_frend'=>$frendId])->all();
            // Флаг подписан на него или нет
            $flagYouFoll = !empty($followers);
            // Если есть запись
            if($flagYouFoll)
            {
                // Записываем id записи
                $idRecord = $followers[0]->id;
            }else{
                $idRecord = null;
            }

            // Ищем все записи где запрашиваемый пользователь подписан на текущего
            $followers2 = Followers::find()->where(['id_user'=> $frendId, 'id_frend'=>$userId])->all();
            // Флаг подписан ли он на вас
            $flagHeFoll = !empty($followers2);

            return ['idRecord'=>$idRecord, 'youFollow'=>$flagYouFoll, 'heFollow'=>$flagHeFoll];

        } catch (\Exception $e) {
            return ['errorText' => $e->getMessage()];
        }
    }

    //get: 'domain/followers/get-my-subscriptions/<id> Получить пользователей на которых подписан
   public function actionGetMySubscriptions($id)
   {
        try 
        {   
            // Ищим всех на кого подписан пользователь
            $followers = Followers::find()->where(['id_user'=> $id])->all();
            // Будет хранить массив пользователей
            $users = [];

            // Перебираем полученный массив
            foreach ($followers as $follower)
            {
                // Ищем пользователя
                $user = Users::findOne(['id'=>$follower->id_frend]);
                // Добавляем в подготовленный массив
                $users[] = ['userId'=>$user->id,'userName'=>$user->username];
            }

            return $users;

        } catch (\Exception $e) {
            return ['errorText' => $e->getMessage()];
        }
    }

    //get: 'domain/followers/get-my-followers/<id> Получить пользователей которые подписаны на меня
   public function actionGetMyFollowers($id)
   {
        try 
        {
            // Запрашиваем всех кто подписан на пользователя
            $followers = Followers::find()->where(['id_frend'=> $id])->all();
            // Тут будет конечный массив пользователей
            $users = [];

            // Перебираем полученный массив
            foreach ($followers as $follower)
            {   
                // Ищем данные пользователей
                $user = Users::findOne(['id'=>$follower->id_user]);
                // Записываем данные в массив
                $users[] = ['userId'=>$user->id,'userName'=> $user->username];
            }

            return $users;

        } catch (\Exception $e) {
            return ['errorText' => $e->getMessage()];
        }
    }

    //delete: 'domain/followers/<idRecord> Отписаться от пользователя
    public function actionDelete($id)
    {
        try 
        {
            // Ищем запись в базе по id
            $record = Followers::findOne(['id'=>$id]);
            // Удаляем запись
            $record->delete();

            return ['idRecord'=>null, 'youFollow'=>false];

        } catch (\Exception $e) {
            return ['errorText' => $e->getMessage()];
        }
    }

    //post: 'domain/followers  Подписаться на пользователя
    public function actionCreate()
    {
        try 
        {
            $userId1 = Yii::$app->request->post('userId1');     // id текущего пользователя
            $userId2 = Yii::$app->request->post('userId2');     // id на кого подписываемся

            // Создаем запись в БД
            $record = new Followers();
            $record->id_user = $userId1;
            $record->id_frend = $userId2;
            $record->save();

            return ['idRecord'=>$record->id, 'youFollow'=>true];

        } catch (\Exception $e) {
            return ['errorText' => $e->getMessage()];
        }
    }
}
