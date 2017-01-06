<?php

namespace api\models;

use Yii;
use yii\base\Model;

class UserDelete extends Model
{
    public $username;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username'], 'required'],
            [['username'], 'checkSame'],
        ];
    }

    public function checkSame($attribute)
    {
        /* @var $user User */
        $user = Yii::$app->user->identity;
        if ($this->$attribute != $user->$attribute) {
            $this->addError($attribute, "{$attribute} \"{$this->$attribute}\" is not the same.");
        }
    }
}
