<?php

namespace frontend\controllers;
use yii\rest\ActiveController;
use yii\helpers\ArrayHelper;

class SidController extends ActiveController
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
        return $actions;
    }

    // post: 'domain/sids' Создание сесси
    public function actionCreate()
    {
        try{
            return ['ok'=>'ok'];        
        }catch(\Exception $e){
            return ['errorText' => $e->getMessage()];
        }
    }

}
