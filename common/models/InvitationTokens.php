<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "invitation_tokens".
 *
 * @property int $id
 * @property string|null $token
 * @property int|null $id_inviting_user
 * @property int|null $id_invited_user
 *
 * @property Users $invitedUser
 * @property Users $invitingUser
 */
class InvitationTokens extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'invitation_tokens';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_inviting_user', 'id_invited_user'], 'integer'],
            [['token'], 'string', 'max' => 250],
            [['id_invited_user'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['id_invited_user' => 'id']],
            [['id_inviting_user'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['id_inviting_user' => 'id']],
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
            'id_inviting_user' => 'Id Inviting User',
            'id_invited_user' => 'Id Invited User',
        ];
    }

    /**
     * Gets query for [[InvitedUser]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInvitedUser()
    {
        return $this->hasOne(Users::class, ['id' => 'id_invited_user']);
    }

    /**
     * Gets query for [[InvitingUser]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInvitingUser()
    {
        return $this->hasOne(Users::class, ['id' => 'id_inviting_user']);
    }
}
