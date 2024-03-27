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
 * @property int|null $id_user_executor
 * @property string|null $date_start
 * @property string|null $date_end
 * @property int|null $id_status
 *
 * @property TaskStatuses $status
 * @property Users $userCreator
 * @property Users $userExecutor
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
            [['id_user_creator', 'id_user_executor', 'id_status'], 'integer'],
            [['date_start', 'date_end'], 'safe'],
            [['token', 'title'], 'string', 'max' => 250],
            [['id_user_creator'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['id_user_creator' => 'id']],
            [['id_user_executor'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['id_user_executor' => 'id']],
            [['id_status'], 'exist', 'skipOnError' => true, 'targetClass' => TaskStatuses::class, 'targetAttribute' => ['id_status' => 'id']],
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
            'id_user_executor' => 'Id User Executor',
            'date_start' => 'Date Start',
            'date_end' => 'Date End',
            'id_status' => 'Id Status',
        ];
    }

    /**
     * Gets query for [[Status]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStatus()
    {
        return $this->hasOne(TaskStatuses::class, ['id' => 'id_status']);
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

    /**
     * Gets query for [[UserExecutor]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserExecutor()
    {
        return $this->hasOne(Users::class, ['id' => 'id_user_executor']);
    }
}
