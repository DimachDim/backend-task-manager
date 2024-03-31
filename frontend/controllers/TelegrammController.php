<?php
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
// models
use common\models\Tasks;
use common\models\Sid;
use common\models\Users;
use common\models\TaskStatuses;

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
            
            // Инициализация переменных
            $textMessage = '';              // Будет хранить сообщение пользователю
            $chatId = '';                   // хранит id чата
            $buttons = '';                  // Хранит отправляемые кнопки
            $cache = Yii::$app->cache;      // для работы с кэшем
            $chatId = '';                   // id чата
            $textMessageFromUser = '';      // сообщение пользователя
            $callback = '';                 // колбек
            $allowedTags = '<b><i><strong><em><a><code><pre><s><u><strike><del>';   // Список тегов поддерживаемых телеграм

            // Если пришло message
            if(isset($data['message']))
            {
                $chatId = $data['message']['chat']['id'];           // id чата
                $textMessageFromUser = $data['message']['text'];    // текст пользователя
            }
            // Если пришло не message
            else{
                $chatId = $data['callback_query']['message']['chat']['id']; // id чата
                $callback = $data['callback_query']['data'];
            }

            // ================ КНОПКИ ===============
            $buttonsTaskLists = json_encode(array(
                'inline_keyboard' => array(
                    array(   
                        array(
                            'text' => 'Созданые мной',
                            'callback_data' => '/mytasks',
                        ),
                
                        array(
                            'text' => 'Назначенные мне',
                            'callback_data' => '/assignedtasks',
                        ),
                    )
                ),
            ));

            // ================ КНОПКИ END ============

            // Ищем в сессиях id чата
            $recordSid = Sid::findOne(['sid'=> $chatId]);
            
            // Если чат неавторизован
            if ($recordSid == null) 
            {
                // Если команда /start
                if($textMessageFromUser == '/start')
                {
                    $textMessage = 'Укажите ваш логин!';
                    // Создаем пустой асоциативный массив
                    $caсheData = ['login'=>'', 'password'=>''];
                    // Записываем его в кэш код ключем чата
                    $cache->set($chatId, $caсheData);
                }

                // Все сообщения кроме /start
                else{
                    // достаем из кэша асоциативный массив
                    $caсheData = $cache->get($chatId);
                    
                    // Если в кэше нет логина
                    if($caсheData['login'] == '')
                    {
                        // По отправленному сообщению ищим логин
                        $recordUsers = Users::findOne(['username'=> $textMessageFromUser]);
                        // Если нет записи с таким логином
                        if($recordUsers == null)
                        {
                            // Формируем сообщение
                            $textMessage = 'Пользователя с таким логином нет. Введите логин!';
                        }
                        // Если есть запись с таким логином
                        else{
                            // Сохраняем логин в кэш
                            $cache->set($chatId, ['login'=>$textMessageFromUser, 'password'=>'']);
                            // Формируем сообщение
                            $textMessage = 'Укажите пароль!';
                        }
                    }
                    // Если в кэше есть логин
                    else{
                        //Получаем логин из кэша
                        $login = $caсheData['login'];
                        // Делаем запрос к базе
                        $recordUsers = Users::findOne(['username'=> $login]);
                        // Если пароль от пользователя совпадает с пароолем в базе
                        if($recordUsers->password == $textMessageFromUser)
                        {
                            // Создаем новую сессию
                            $sid = new Sid();
                            $sid->sid = strval($chatId);        // В качестве сессии указываем id чата
                            $sid->id_user = $recordUsers->id;
                            $sid->save();
                            // очищаем кэш
                            $cache->delete($chatId);
                            // назначаем кнопки
                            $buttons = $buttonsTaskLists;
                            // Устанавливаем текст
                            $textMessage = 'Какие задачи показать?';
                        }
                        // Если пароль не совпадает
                        else{
                            $textMessage = 'Неверный пароль. Введите пароль!';
                        }
                    }                   
                }
            }

            // Если чат авторизован
            else{
                
                // Смотрим колбеки
                switch ($callback) {
                    // Мои задачи
                    case '/mytasks':

                        // Достаем id пользователя из базы
                        $userId = Sid::findOne(['sid'=> $chatId])->id_user;
                        // Ищим все задачи созданые им
                        $tasks = Tasks::find()->where(['id_user_creator'=> $userId])->all();
                        // Перебираем полученный масив
                        foreach($tasks as $task)
                        {
                            // Формируем текст
                            $textMessage .= 
                            // Заголовок
                            "<b>" . $task->title . "</b>" . "\n \n" .
                            // Текст
                            str_replace(
                                '·&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',    // Убираем из теста такие теги
                                 "\n - ",                                   // заменяя на такие
                                 strip_tags($task->text, $allowedTags)      // Убираем теги которых нет в телеграм
                            ). "\n \n" .
                            // Токен
                            'Токен: ' . $task->token . "\n".
                            // Остальные данные
                            'Исполнитель: '. Users::findOne(["id"=> $task->id_user_executor])->username . "\n".
                            'Дата начала: '. $task->date_start . "\n".
                            'Дата окончания: '. $task->date_end . "\n".
                            'Статус: ' . TaskStatuses::findOne(['id'=> $task->id_status])->statusName ."\n".
                            "\n \n";     // Отступы
                            
                            // Устанавливаем кнопки
                            $buttons = $buttonsTaskLists;
                        }

                        break;

                    // Задачи назначенные мне
                    case '/assignedtasks':
                        // Достаем id пользователя из базы
                        $userId = Sid::findOne(['sid'=> $chatId])->id_user;
                        // Ищим все задачи созданые им
                        $tasks = Tasks::find()->where(['id_user_executor'=> $userId])->all();
                        // Перебираем полученный масив
                        foreach($tasks as $task)
                        {
                            // Формируем текст
                            $textMessage .= 
                            // Заголовок
                            "<b>" . $task->title . "</b>" . "\n \n" .
                            // Текст
                            str_replace(
                                '·&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',    // Убираем из теста такие теги
                                 "\n - ",                                   // заменяя на такие
                                 strip_tags($task->text, $allowedTags)      // Убираем теги которых нет в телеграм
                            ). "\n \n" .
                            // Токен
                            'Токен: ' . $task->token . "\n".
                            // Остальные данные
                            'Создатель: '. Users::findOne(["id"=> $task->id_user_creator])->username . "\n".
                            'Дата начала: '. $task->date_start . "\n".
                            'Дата окончания: '. $task->date_end . "\n".
                            'Статус: ' . TaskStatuses::findOne(['id'=> $task->id_status])->statusName ."\n".
                            "\n \n";     // Отступы
                            
                            // Устанавливаем кнопки
                            $buttons = $buttonsTaskLists;
                        }

                        break;

                    // В остальных случаях
                    default:
                        // Нзаначаем кнопки. Листы задач
                        $buttons = $buttonsTaskLists;
                        // Устанавливаем текст
                        $textMessage = 'Какие задачи показать?';
                }
            }
            
            
            Yii::$app->telegram->sendMessage([
                'chat_id' => $chatId, // ID чата, куда отправить сообщение
                'text' => $textMessage,
                'reply_markup' => $buttons,
                'parse_mode' => 'HTML'
            ]);
            
        }catch(\Exception $e){
            //writeLogFile($e->getMessage());
            Yii::$app->telegram->sendMessage([
                'chat_id' => 1875864140, // ID чата, куда отправить сообщение
                'text' => $e->getMessage()
            ]);
        }
    }
    
}