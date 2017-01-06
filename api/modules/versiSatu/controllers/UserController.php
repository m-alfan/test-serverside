<?php

namespace api\modules\versiSatu\controllers;

use api\components\Controller;
use api\models\ChangeAccount;
use api\models\ChangePassword;
use api\models\search\UserSearch;
use api\models\User;
use api\models\UserDelete;
use api\models\UserToken;
use Yii;

class UserController extends Controller
{
    protected function verbs()
    {
        return [
            'index'           => ['GET'],
            'signup'          => ['POST', 'PUT'],
            'change-password' => ['POST', 'PUT'],
            'change-account'  => ['POST', 'PUT'],
            'delete'          => ['DELETE'],
        ];
    }

    public function actionIndex()
    {
        $search['UserSearch'] = Yii::$app->request->queryParams;

        $searchModel  = new UserSearch();
        $dataProvider = $searchModel->search($search);

        $dataModels = [];
        foreach ($dataProvider->models as $v) {
            $dataModels[] = [
                'id'         => $v->id,
                'username'   => $v->username,
                'email'      => $v->email,
                //'auth_key'   => $v->auth_key,
                //'token'      => $v->userToken->token,
                'created_at' => date('Y-m-d H:i:s', $v->created_at),
                'updated_at' => date('Y-m-d H:i:s', $v->updated_at),
            ];
        }

        return [
            'page'       => $dataProvider->pagination->page + 1,
            'pageSize'   => $dataProvider->pagination->pageSize,
            'count'      => $dataProvider->count,
            'totalCount' => $dataProvider->totalCount,
            'dataModels' => $dataModels,
        ];
    }

    public function actionSignup()
    {
        $user = new User();

        $user->username = Yii::$app->request->post('username');
        $user->email    = Yii::$app->request->post('email');
        $user->setPassword(Yii::$app->request->post('password'));

        $user->generateAuthKey();
        $user->status = $user::STATUS_ACTIVE;
        return Yii::$app->getDb()->transaction(function () use ($user) {
            if ($user->validate() && $user->save()) {
                $token = new UserToken([
                    'user_id' => $user->id,
                    'token'   => $user->generateToken(),
                    'expire'  => $user->generateExpireToken(),
                ]);

                return true;
            }

            $this->responError();
            return $user->errors;
        });
    }

    public function actionChangePassword()
    {
        $model                  = new ChangePassword();
        $post['ChangePassword'] = Yii::$app->request->post();

        if ($model->load($post) && $model->change()) {
            return true;
        }

        $this->responError();
        return $model->errors;
    }

    public function actionChangeAccount()
    {
        $user  = Yii::$app->user->identity;
        $model = new ChangeAccount([
            'username' => $user->username,
            'email'    => $user->email,
        ]);

        $post['ChangeAccount'] = Yii::$app->request->post();

        if ($model->load($post) && $model->change()) {
            return true;
        }

        $this->responError();
        return $model->errors;
    }

    public function actionDelete()
    {
        $model              = new UserDelete();
        $post['UserDelete'] = Yii::$app->request->post();

        if ($model->load($post) && $model->validate()) {
            return !$this->findModel()->delete() ?: true;
        }

        $this->responError();
        return $model->errors;
    }

    protected function findModel()
    {
        if (($model = User::findOne(Yii::$app->user->id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
