<?php

namespace frontend\controllers;
use Yii;
use yii\rest\ActiveController;
use yii\helpers\ArrayHelper;

class TasksController extends ActiveController
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

    // post: 'domain/tasks' Создание сесси
    public function actionCreate()
    {
        try{
            
            throw new \Exception('');
            

        }catch(\Exception $e){
            return ['errorText' => $e->getMessage()];
        }
    }
}
