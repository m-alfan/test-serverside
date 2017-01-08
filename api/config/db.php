<?php
//mysql
/*return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=localhost;dbname=server-side',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8',
];*/
//sqlite
return [
    'class'    => 'yii\db\Connection',
    'dsn'      => 'sqlite:@runtime/main-db.sqlite',
    'username' => 'root',
    'password' => '',
    'charset'  => 'utf8',
];