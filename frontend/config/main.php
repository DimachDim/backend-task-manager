<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'components' => [

        'corsFilter' => [
            'class' => \yii\filters\Cors::class,
            'cors' => [
                'Origin' => ['*'], // You can specify more origins here
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'], // You can specify the allowed headers here
                'Access-Control-Allow-Credentials' => true,
                'Access-Control-Max-Age' => 86400,
            ],
        ],
        
                'request' => [
                    'enableCsrfCookie' => false,
                    'parsers' => [
                        'application/json' => yii\web\JsonParser::class,
                    ]
                ],

        'as corsFilter' => [
            'class' => \yii\filters\Cors::class,
            'cors' => [
                'Origin' => ['http://example.com', 'https://example.com'], // Replace with your actual domain(s)
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'],
                'Access-Control-Allow-Credentials' => true,
                'Access-Control-Max-Age' => 86400,
                'Access-Control-Expose-Headers' => [],
            ],
        ],
        
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-frontend',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => \yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                // Получение инф о пользователе
                ['class' => 'yii\rest\UrlRule', 'controller' => 'users', 'extraPatterns' => ['GET get-info/<sid>' => 'get-info']],
                // Получение списка приглашенных пользователей
                ['class' => 'yii\rest\UrlRule', 'controller' => 'users', 'extraPatterns' => ['GET get-invited/<id>' => 'get-invited']],
                // Получение списка пользователей по имени
                ['class' => 'yii\rest\UrlRule', 'controller' => 'users', 'extraPatterns' => ['GET serch-name/<userName>' => 'get-user-by-name']],
                
                // Получение списка задач созданых пользователем
                ['class' => 'yii\rest\UrlRule','controller' => 'tasks', 'extraPatterns' => ['GET my-tasks/<id>' => 'my-tasks']],
                // Получение списка задач назначенных мне
                ['class' => 'yii\rest\UrlRule','controller' => 'tasks', 'extraPatterns' => ['GET pending-tasks/<id>' => 'pending-tasks']],
                
                // Ответ подписан на пользователя или нет
                ['class' => 'yii\rest\UrlRule','controller' => 'followers', 'extraPatterns' => ['GET yes-no/<userId>/<frendId>' => 'yes-no']],
                // Получить пользователей на которых подписан 
                ['class' => 'yii\rest\UrlRule','controller' => 'followers', 'extraPatterns' => ['GET get-my-subscriptions/<id>' => 'get-my-subscriptions']],
                // Получить список подписчиков 
                ['class' => 'yii\rest\UrlRule','controller' => 'followers', 'extraPatterns' => ['GET get-my-followers/<id>' => 'get-my-followers']],
                
                ['class' => 'yii\rest\UrlRule', 'controller' => 'sid'],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'tasks'],
                ['class' => 'yii\rest\UrlRule', 'controller' => 'invite-tokens'],
            ],
        ]
    ],
    'params' => $params,
];
