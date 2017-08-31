<?php
/**
 * Created by PhpStorm.
 * User: lex
 * Date: 02.12.16
 * Time: 16:40
 */

namespace common\components\klbase;

use yii\db\ActiveRecord;
use yii\base\Behavior;


use stdClass;

class KlbaseBehavior extends Behavior
{
    // Компонент - справочников
    public $obj;

    /*
     * Поля - привязка к спарвочникам
     * [
     *      [FieldName, BaseName],
     *      ....
     * ]
     */
    public $fields = [];


    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
    }

    /**
     * @return array
     */
    public function events(){
        return [
            ActiveRecord::EVENT_BEFORE_VALIDATE => 'bSave',
            ActiveRecord::EVENT_BEFORE_INSERT   => 'bSave',
            ActiveRecord::EVENT_BEFORE_UPDATE    => 'bSave',
            ActiveRecord::EVENT_AFTER_FIND      => 'aFind',
            ActiveRecord::EVENT_INIT            => 'aInit',
        ];
    }

    /**
     *  После загрузки модели из БД
     */
    public function aFind()
    {
        foreach ($this->fields as $field){
            // Необязательный 3 параметр (по умолчанию true), включает Заполнение пустого значения - значением по умолчанию
            if( ! isset($field[2]) ) $field[2] = true;
            $this->loadFieldToBase($field[0], $field[1], $field[2]);
        }
    }


    /**
     *  При инициализации модели
     *  Только если новая запись
     */
    public function aInit()
    {
        if( $this->owner->isNewRecord ){
            foreach ($this->fields as $field){
                // Необязательный 3 параметр (по умолчанию true), включает Заполнение пустого значения - значением по умолчанию
                if( ! isset($field[2]) ) $field[2] = true;
                $this->loadFieldToBase($field[0], $field[1], $field[2]);
            }
        }
    }

    /**
     * Преобразование в скаляр перед сохранением
     */
    public function bSave()
    {
        foreach ($this->fields as $field){
            if( is_object($this->owner->{$field[0]}) ){
                if( isset( $this->owner->{$field[0]}->id ) ){
                    $this->owner->{$field[0]} = $this->owner->{$field[0]}->id;
                }
            }
        }
    }


    protected function loadFieldToBase($fieldName, $baseName, $isEmpty = true)
    {
        // Если пустое значение + отключенно Default заполнение, оставляем NULL
        if( empty($this->owner->{$fieldName}) && (!$isEmpty) ){
            return;
        }

        if( empty($this->owner->{$fieldName}) ){
            // определяем зависимость
            $relation = $this->obj->{$baseName}->getStructRelation();
            if(trim($relation) != '') $relation = $this->obj->{$relation}->getDefault();
            else $relation = null;

            $this->owner->{$fieldName} = $this->obj->{$baseName}->getDefault($relation);
        }

        $this->owner->{$fieldName} = $this->obj->{$baseName}->getRecord($this->owner->{$fieldName});
    }





}