<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use common\models\Tasks;

include '../functions/writeLogFile.php';

// get : domain/telegram
class TelegrammController extends Controller
    {
    
    public $enableCsrfValidation = false;

    public function actions()
    {
        $actions = parent::actions();
        // Указываем какие экшены будут
        unset($actions['webhook']);

        return $actions;
    }

    public function beforeAction($action)
    {
        // отключаем проверку CSRF для заданного действия
        if ($action->id === 'webhook') {
            $this->enableCsrfValidation = false;
        }
        return parent::beforeAction($action);
    }

    // domain/frontend/api/telegramm/webhook
    public function actionWebhook()
    {
        try{
            
            // получаем данные POST
            $postdata = file_get_contents('php://input');
            // декодируем данные из формата JSON
            $data = json_decode($postdata, true);

            // Достаем необходимые данные
            $chatId = $data['message']['chat']['id'];           // id чата

            


            writeLogFile($data, true);

            //$text = Tasks::findOne(['id'=>39])->text;

            Yii::$app->telegram->sendMessage([
                'chat_id' => 1875864140, // ID чата, куда отправить сообщение
                'text' => $chatId,
            ]);

        }catch(\Exception $e){
            return ['errorText' => $e->getMessage()];
        }
    }
    
}