<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "followers".
 *
 * @property int $id
 * @property int|null $id_user
 * @property int|null $id_frend
 *
 * @property Users $frend
 * @property Users $user
 */
class Followers extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'followers';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_user', 'id_frend'], 'integer'],
            [['id_user'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['id_user' => 'id']],
            [['id_frend'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['id_frend' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_user' => 'Id User',
            'id_frend' => 'Id Frend',
        ];
    }

    /**
     * Gets query for [[Frend]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFrend()
    {
        return $this->hasOne(Users::class, ['id' => 'id_frend']);
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
