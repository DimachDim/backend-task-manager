<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "tasks".
 *
 * @property int $id
 * @property string $token
 * @property string $title
 * @property string $text
 * @property int|null $id_user_creator
 *
 * @property Users $userCreator
 */
class Tasks extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tasks';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['token', 'title', 'text'], 'required'],
            [['text'], 'string'],
            [['id_user_creator'], 'integer'],
            [['token', 'title'], 'string', 'max' => 250],
            [['id_user_creator'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['id_user_creator' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'token' => 'Token',
            'title' => 'Title',
            'text' => 'Text',
            'id_user_creator' => 'Id User Creator',
        ];
    }

    /**
     * Gets query for [[UserCreator]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserCreator()
    {
        return $this->hasOne(Users::class, ['id' => 'id_user_creator']);
    }
}
