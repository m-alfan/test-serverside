<?php

namespace api\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 *
 * @property UserContact[] $contacts
 */
class User extends ActiveRecord implements IdentityInterface
{
    public $password;

    const STATUS_PANDING    = 0;
    const STATUS_ACTIVE     = 10;
    const STATUS_DELETED    = 20;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }
    
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['username', 'filter', 'filter' => 'trim'],
            ['username', 'required'],
            ['username', 'in', 'not' => true,
                'range' => ['admin', 'administrator', 'superadmin', 'super', 'root']],
            ['username', 'unique', 'message' => 'This username has already been taken.'],
            ['username', 'string', 'min' => 2, 'max' => 255],
            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'unique', 'message' => 'This email address has already been taken.'],
            ['password', 'required', 'when' => function ($model) {
              return empty($model->password_hash);
            }],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }
    
    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['auth_key' => $token, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::find()
            ->where(['status' => self::STATUS_ACTIVE])
            ->andWhere(['OR', ['username' => $username], ['email' => $username]])
            ->one();
    }
    
    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        return static::findOne([
            'password_reset_token' => $token,
            'status'               => self::STATUS_ACTIVE,
        ]);
    }



    public static function getUniqueUsername($username)
    {
        $i    = 1;
        $name = $username;
        while (static::find()->where(['username' => $name])->exists()) {
            $name = $username . $i++;
        }
        return $name;
    }
    
    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }
    
    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }
    
    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }
    
    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }
    
    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        if(empty($password))
            return;
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }
    
    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString().time();
    }

    /**
     * Generate token
     */
    public function generateToken()
    {
        return Yii::$app->security->generateRandomString();
    }

    public function generateExpireToken()
    {
        return (time() + Yii::$app->params['tokenExpire']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserToken()
    {
        return $this->hasOne(UserToken::className(), ['user_id' => 'id']);
    }
}