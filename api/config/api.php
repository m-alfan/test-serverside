<?php

$params         = require __DIR__ . '/params.php';
$ruleController = require __DIR__ . '/ruleController.php';
$db             = require __DIR__ . '/db.php';

$config = [
    'id'         => 'coreApi',
    'name'       => 'coreApi',

    'basePath'   => dirname(__DIR__),
    'bootstrap'  => ['log'],

    'aliases'    => [
        '@api'    => dirname(__DIR__),
        '@vendor' => dirname(dirname(__DIR__)) . '/vendor',
        '@bower'  => '@vendor/bower',
    ],

    //waktu aplikasi
    'timeZone'   => 'Asia/Jakarta',

    'modules'    => [
        'v1' => [
            'basePath' => '@api/modules/versiSatu',
            'class'    => 'api\modules\versiSatu\v1',
        ],
    ],

    'components' => [
        'request'    => [
            'cookieValidationKey' => '0bc3a55bcdca28e6de39b6257150d26f',
            // Enable JSON Input:
            'parsers'             => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'response'   => [
            'class'         => 'api\components\Response',
            //buat default respon
            'on beforeSend' => function ($event) {
                $response = $event->sender;
                if ($response->data !== null) {
                    if (isset($response->data['type'])) {
                        unset($response->data['type']);
                    }

                    //struktur respon utama
                    $response->data = [
                        'status' => $response->isSuccessful && $response->dataOk ? 'success' : 'error',
                        'access' => [
                            'token'   => $response->token,
                            'expired' => $response->expired,
                        ],
                        'data'   => $response->data,
                    ];
                    $response->statusCode = 200;
                }
            },
            'format'        => \yii\web\Response::FORMAT_JSON,
            'charset'       => 'UTF-8',
        ],
        'user'       => [
            'identityClass' => 'api\models\User',
            'loginUrl'      => null,
            'enableSession' => false,
        ],
        'log'        => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets'    => [
                [
                    'class'   => 'yii\log\FileTarget',
                    'levels'  => ['error', 'warning'],
                    // But in file 'api.log':
                    'logFile' => '@api/runtime/logs/api.log',
                ],
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl'     => true,
            'enableStrictParsing' => true,
            'showScriptName'      => false,
            'rules'               => [
                [
                    'class'         => 'yii\rest\UrlRule',
                    'pluralize'     => false,
                    'controller'    => ['v1'],
                    'extraPatterns' => $ruleController['v1'],
                ],
            ],
        ],
        'db'         => $db,
        'mailer'     => [
            'class'            => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
    ],
    'params'     => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][]      = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];

    $config['bootstrap'][]    = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
