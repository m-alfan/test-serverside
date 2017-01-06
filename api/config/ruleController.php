<?php
return [
    'v1' => [
        ''                 => 'about/index',
        
        //login untuk mendapatkan token
        'login'            => 'login/get-token',

        //akses api user
        'signup'           => 'user/signup',
        'user'             => 'user/index',
        'change-password'  => 'user/change-password',
        'change-account'   => 'user/change-account',
        'delete'           => 'user/delete',
    ],
];
