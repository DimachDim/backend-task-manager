<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "sid".
 *
 * @property int $id
 * @property string $sid
 * @property int|null $id_user
 *
 * @property Users $user
 */
class Sid extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sid';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['sid'], 'required'],
            [['id_user'], 'integer'],
            [['sid'], 'string', 'max' => 250],
            [['id_user'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['id_user' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'sid' => 'Sid',
            'id_user' => 'Id User',
        ];
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::class, ['id' => 'id_user']);
    }
}
