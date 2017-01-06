<?php

namespace api\modules\versiSatu\controllers;

use api\components\Controller;
use api\models\LoginForm;
use api\models\UserToken;
use Yii;

class LoginController extends Controller
{
    /**
     * Validasi method request padasetiap action
     *
     * @return array
     */
    protected function verbs()
    {
        return [
            'get-token' => ['POST', 'PUT'],
        ];
    }

    /**
     * Action untuk login
     *
     * jika berhasil return berupa data profile user,
     * jika gagal retun berupa keterangan gagal
     *
     * @return array profileUser | errorDesc
     */
    public function actionGetToken()
    {
        //get post request
        $data['LoginForm'] = Yii::$app->request->post();

        $model = new LoginForm();
        $model->load($data);
        if ($model->validate()) {
            $accessToken = $model->_user;

            //save new expire token user
            if ($accessToken->userToken === null) {
                $newToken          = new UserToken;
                $newToken->user_id = $accessToken->id;
            } else {
                $newToken = $accessToken->userToken;
            }

            $newToken->token  = $accessToken->generateToken();
            $newToken->expire = $accessToken->generateExpireToken();
            $newToken->save();

            return [
                'id'       => $accessToken->id,
                'username' => $accessToken->username,
                'email'    => $accessToken->email,
                'auth_key' => $accessToken->auth_key,
                'token'    => $newToken->token,
                'expire'   => $newToken->expire,
            ];
        }

        $this->responError();
        return $model->errors;
    }
}
