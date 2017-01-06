<?php

namespace api\components;

class Response extends \yii\web\Response
{
    /**
     * Token request
     *
     * @var null
     */
    public $token = null;

    /**
     * Data yang tidak sesuai
     *
     * @var boolean
     */
    public $dataOk = true;

    /**
     * Dipakai untuk masa belaku token
     *
     * @var null
     */
    public $expired = null;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
    }
}
