<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "users".
 *
 * @property int $id
 * @property string $username
 * @property string $password
 *
 * @property InvitationTokens[] $invitationTokens
 * @property InvitationTokens[] $invitationTokens0
 * @property Sid[] $ss
 * @property Tasks[] $tasks
 */
class Users extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'password'], 'required'],
            [['username', 'password'], 'string', 'max' => 250],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'password' => 'Password',
        ];
    }

    /**
     * Gets query for [[InvitationTokens]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInvitationTokens()
    {
        return $this->hasMany(InvitationTokens::class, ['id_invited_user' => 'id']);
    }

    /**
     * Gets query for [[InvitationTokens0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInvitationTokens0()
    {
        return $this->hasMany(InvitationTokens::class, ['id_inviting_user' => 'id']);
    }

    /**
     * Gets query for [[Ss]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSs()
    {
        return $this->hasMany(Sid::class, ['id_user' => 'id']);
    }

    /**
     * Gets query for [[Tasks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTasks()
    {
        return $this->hasMany(Tasks::class, ['id_user_creator' => 'id']);
    }
}
