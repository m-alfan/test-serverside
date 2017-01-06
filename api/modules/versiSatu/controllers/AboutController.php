<?php

namespace api\modules\versiSatu\controllers;

use api\components\Controller;
use Yii;

class AboutController extends Controller
{
    /**
     * Validasi method request padasetiap action
     *
     * @return array
     */
    protected function verbs()
    {
        return [
            'index' => ['GET'],
        ];
    }

    /**
     * Menampilkan informasi general mengenai api
     *
     * @return array
     */
    public function actionIndex()
    {
        $params = Yii::$app->params;
        return [
            'name'        => $params['name'],
            'description' => $params['description'],
            'version'     => $params['version'],
            'baseUrl'     => $this->baseUrl(),
            'accessLogin' => [
                'url'    => $this->baseUrl() . '/v1/login',
                'params' => ['username', 'password'],
            ],
        ];
    }
}
