<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 06.07.2016
 * Time: 10:34
 */

namespace common\components\klbase\models;

use yii\base\Model;


/**
 * This is the model class for Serialize struct "fields" of table "{{%klbase_bases}}".
 *
 * @property string $name
 * @property string $title
 * @property string $type
 */

class BasesFieldsItem extends Model
{

    public $name;

    public $title;

    public $type;

    public $required;

    /** @inheritdoc */
    public function attributeLabels()
    {
        return [
            'name'  => 'Системное имя',
            'title'   => 'Название/Описание',
            'type'    => 'Тип',
            'required'    => 'Обязательно к заполнению',
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'title', 'type'], 'required'],
            [['type'], 'integer'],
            [['required'], 'boolean'],
            ['name', 'string', 'max' => 14],
            ['title', 'string', 'max' => 36],
        ];
    }


}