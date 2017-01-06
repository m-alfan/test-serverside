<?php

namespace api\components;

use api\models\user\UserToken;
use Yii;
use yii\web\UnauthorizedHttpException;

/**
 * Authentifikasi user access extend dari \yii\filters\auth\AuthMethod
 *
 * @author Muhamad Alfan <muhamad.alfan01@gmail.com>
 * @since 1.0
 */
class Auth extends \yii\filters\auth\AuthMethod
{
    /**
     * Allowed url
     *
     * @var array
     */
    private $allowedUrl = ['v1', 'v1/login', 'v1/signup'];

    /**
     * Parameter untuk header request
     * @var string
     */
    public $authKey = 'auth-key';

    /**
     * Parameter untuk header request
     * @var string
     */
    public $tokenParam = 'token';

    /**
     * @inheritdoc
     */
    public function authenticate($user, $request, $response)
    {
        //get header param
        $accessAuthKey = $request->getHeaders()->get($this->authKey);
        $accessToken   = $request->getHeaders()->get($this->tokenParam);

        //cek access login
        $accessAllowed = $this->accessAllowed($request, $accessAuthKey, $accessToken);
        if ($accessAllowed) {
            return $accessAllowed;
        }

        //jika param bukan string return error
        if (!is_string($accessAuthKey) || !is_string($accessToken)) {
            $this->handleFailure($response);
        }

        //default url yang di perbolehkan
        $allowed = false;

        //login by token
        $identity = $user->loginByAccessToken($accessAuthKey, get_class($this));

        //just for development
        if (YII_ENV_DEV && $identity !== null) {
            return $identity;
        }

        //invalid auth key
        if ($identity !== null && isset($identity->userToken)) {
            $allowed = true;
        } else {
            throw new UnauthorizedHttpException(Yii::t('app', 'You are requesting with an invalid Auth Key.'));
        }

        //invalid token
        if ($allowed && ($accessToken !== $identity->userToken->token)) {
            throw new UnauthorizedHttpException(Yii::t('app', 'You are requesting with an invalid Token.'));
        }

        //token expired
        if ($allowed && (time() >= $identity->userToken->expire)) {
            throw new UnauthorizedHttpException(Yii::t('app', 'You are requesting with an expired Token.'));
        }

        //set new token
        if ($allowed) {
            $newToken         = $identity->userToken;
            $newToken->token  = $identity->generateToken();
            $newToken->expire = $identity->generateExpireToken();

            $newToken->validate() ? $newToken->save() : null;

            //send new token
            $response->token   = $identity->userToken->token;
            $response->expired = $identity->userToken->expire;

            return $identity;
        }

        $this->handleFailure($response);
    }

    /**
     * Proses url yang diperbolehkan
     * Jika request bukan salah satu url yang diperbolehkan, atau token beserta authKey
     * tidak sesuai dengan tokenDefault maka proses authentifikasi salah
     *
     * @param  $request
     * @param  $accessAuthKey
     * @param  $accessToken
     * @return boolean
     */
    protected function accessAllowed($request, $accessAuthKey, $accessToken)
    {
        $defaultToken = Yii::$app->params['tokenDefault'];
        $allowed      = false;

        //check url yang diperbolehkan
        if (in_array($request->pathInfo, $this->allowedUrl)) {
            $allowed = true;
        }

        //check token default
        if ($allowed && ($accessAuthKey === $defaultToken || $accessToken === $defaultToken)) {
            return true;
        }

        return false;
    }
}
