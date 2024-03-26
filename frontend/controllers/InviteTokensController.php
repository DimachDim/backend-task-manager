<?php

namespace frontend\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\helpers\ArrayHelper;
include '../functions/generateRandomString.php';
//models
use common\models\InvitationTokens;
use common\models\Users;

class InviteTokensController extends ActiveController
{
    public $modelClass = 'common\models\InvitationTokens';

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

    // post: 'domain/invite-tokens' Создание нового токена приглашения
    public function actionCreate()
    {
        try{
            
            // id пользователя
            $userId = Yii::$app->request->post('userId');
            // Смотрим надо ли генирировать новый токен
            $generateNew = Yii::$app->request->post('generateNew');
            $tokenRandom = null;    // По умолчанию пуст

            // Если надо генерировать
            if($generateNew)
            {
                // Создаем токен
                $tokenRandom = generateRandomString(10, '#');
                // Создаем запись в бд с таким токеном
                $inviteToken = new InvitationTokens();
                $inviteToken->id_inviting_user = $userId;
                $inviteToken->id_invited_user = null;
                $inviteToken->token = $tokenRandom;
                $inviteToken->save();
            }
            
            // Получаем все созданные пользователем токены
            $userTokens = InvitationTokens::findAll(['id_inviting_user'=>$userId]);
            $newUserTokens = [];    // Для копирования массива

            // Перебераем полученный массив
            foreach($userTokens as $token)
            {   
                // Конвертируем объект в массив
                $newToken = $token->attributes;
                // Если есть приглашенный пользователь
                if($token->id_invited_user != null)
                {
                    // Добавляем новое свойство и запрашиваем в базе имя пользователя
                    $newToken['name_invited_user'] = Users::findOne(['id' => $token->id_invited_user])->username;
                }

                // Добавляем измененый объект в массив
                $newUserTokens[] = $newToken;
            }

            
            // Возвращаем новый токен и все токены пользователя
            return ['token' => $tokenRandom, 'tokens' => $newUserTokens];

           

        }catch(\Exception $e){
            return ['errorText' => $e->getMessage()];
        }
    }

    

}
