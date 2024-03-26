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
            
            return $frendId;

        } catch (\Exception $e) {
            return ['errorText' => $e->getMessage()];
        }
    }
}
