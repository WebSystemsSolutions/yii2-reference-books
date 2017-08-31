<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 06.07.2016
 * Time: 10:34
 */

namespace common\components\klbase\models;

use Yii;
use yii\db\ActiveRecord;

use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use common\behaviors\StructListFieldBehavior;

/**
 * This is the model class for table "{{%klbase_bases}}".
 *
 * @property integer $id
 * @property string $name
 * @property string $title
 * @property string $fields
 * @property string $relation
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $created_by
 * @property integer $updated_by
 */

class Bases extends ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%klbase_bases}}';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
            BlameableBehavior::className(),
            [
                'class'     =>  StructListFieldBehavior::className(),
                'field'     =>  'fields',
                'modelClass'     =>  BasesFieldsItem::className(),
            ],
        ];
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'title'], 'required'],
            ['name', 'match', 'pattern' => '/^[A-z]+$/', 'message' => Yii::t('klbase', 'System name must only contain Latin characters')],
            [['name'], 'unique'],
            [['created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['name', 'relation'], 'string', 'max' => 14],
            [['title'], 'string', 'max' => 36],
            [['fields'], 'string'],
        ];
    }

    /** @inheritdoc */
    public function attributeLabels()
    {
        return [
            'name'  => Yii::t('klbase', 'System directory name'),
            'title'   => Yii::t('klbase', 'Name / Description'),
            'fields'    => Yii::t('klbase', 'Ext fields'),
            'relation'    => Yii::t('klbase', 'Dependent reference (parent)'),
            'created_at' => Yii::t('main', 'Time of creation'),
            'updated_at' => Yii::t('main', 'Time of change'),
            'created_by' => Yii::t('main', 'Creator'),
            'updated_by' => Yii::t('main', 'Editor'),
        ];
    }

    public function beforeDelete()
    {
        // Удаляем данные справочника
        KlbaseData::deleteAll(['base' => $this->name]);

        return parent::beforeDelete(); // TODO: Change the autogenerated stub
    }

    /**
     * @return bool
     *
     * Валидация удаления справочника
     * Проверка на зависимости
     */
    public function deleteValidate()
    {
        $relation = self::find()->where(['relation' => $this->name])->all();
        if($relation == null) return true;
        else return false;
    }

}