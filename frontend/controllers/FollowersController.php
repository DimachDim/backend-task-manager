<?php

namespace frontend\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\helpers\ArrayHelper;
include '../functions/generateRandomString.php';

// models
use common\models\Followers;


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
        unset($actions['update']);   
        unset($actions['delete']);   
        return $actions;
    }

   //get: 'domain/followers/yes-no/<userId>/<frendId>
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
}
