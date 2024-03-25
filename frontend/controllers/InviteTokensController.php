<?php

namespace frontend\controllers;

use Yii;
use yii\rest\ActiveController;
use yii\helpers\ArrayHelper;

use common\models\InvitationTokens;

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
            
            

            return 'ok';

        }catch(\Exception $e){
            return ['errorText' => $e->getMessage()];
        }
    }

    

}
