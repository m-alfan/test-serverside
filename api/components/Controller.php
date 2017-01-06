<?php

namespace api\components;

use Yii;

/**
 * Controller default yang digunakan extend dari \yii\rest\Controller
 *
 * @author Muhamad Alfan <muhamad.alfan01@gmail.com>
 * @since 1.0
 */
class Controller extends \yii\rest\Controller
{
    use TraitController;
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors                  = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => 'api\components\Auth',
        ];

        return $behaviors;
    }

    /**
     * Kasih tanda kalau respon error
     */
    public function responError($return = false)
    {
        Yii::$app->response->dataOk = $return;
    }
}
