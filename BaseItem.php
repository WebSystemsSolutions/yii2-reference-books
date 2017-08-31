<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 06.07.2016
 * Time: 9:41
 */

namespace common\components\klbase;

use Yii;
use yii\helpers\ArrayHelper;


use yii\web\MethodNotAllowedHttpException;


class BaseItem
{

    // Данные справочника
    private $base = [
        1 => ['value', 'title', 'relation']
    ];

    // Структура справочника (описание)
    private $struct = [];

    // Зависимый справочник
    private $relation = null;


    public function __construct($base, $struct, $relation)
    {
        $this->base = $base;
        $this->struct = $struct;
        $this->relation = $relation;

        // Индексируем доп.поля
        if(is_array($struct['fields'])) {
            foreach ($struct['fields'] as $field) {
                $this->struct['fields'][$field['name']] = $field;
            }
        }
    }


    /**
     * @param $name
     * @param null $params
     * @return array|null
     * @throws MethodNotAllowedHttpException
     */
    public function __call($name, $params = null)
    {
        $field = lcfirst( str_replace('get', '', $name) );

        $id = isset($params[0]) ? $params[0] : null;
        $relation_id = isset($params[1]) ? $params[1] : null;
        $ext_where = isset($params[2]) ? $params[2] : null;

        return $this->getField($field, $id, $relation_id, $ext_where);
    }


    /**
     * @return string
     *
     * Структура
     * Возвращает Название справочника
     */
    public function getStructTitle()
    {
        return $this->struct['title'];
    }


    /**
     * @return string
     *
     * Структура
     * Возвращает имя справочника - родителя зависимости
     */
    public function getStructRelation()
    {
        return $this->struct['relation'];
    }


    /**
     * @param null $id
     * @return array|null
     *
     * Возвращаем значение Элемента справочника по ИД либо список всех значений справочника
     */
    public function getValue($id = null, $relation_id = null, $ext_where = null)
    {
        if($id == null){
            return ArrayHelper::getColumn($this->findAll($relation_id, $ext_where), 'value' );
        }elseif( isset($this->base[$id]) ){
            return $this->base[$id]['value'];
        }else{
            return null;
        }
    }


    /**
     * @param null $id
     * @return array|null
     *
     * Возвращаем заголовок Элемента справочника по ИД либо список всех заголовков справочника
     */
    public function getTitle($id = null, $relation_id = null, $ext_where = null)
    {
        if($id == null){
            return ArrayHelper::getColumn($this->findAll($relation_id, $ext_where), 'title' );
        }elseif( isset($this->base[$id]) ){
            return $this->base[$id]['title'];
        }else{
            return null;
        }
    }

    /**
     * @param null $id
     * @return null
     *
     * Возвращает ID зависимого справочника по ID либо список
     * @throws MethodNotAllowedHttpException
     */
    public function getRelation($id = null, $relation_id = null, $ext_where = null)
    {
        if($id == null){
            return ArrayHelper::getColumn($this->findAll($relation_id, $ext_where), 'relation' );
        }elseif( isset($this->base[$id]) ){
            return $this->base[$id]['relation'];
        }else{
            return null;
        }
    }


    /**
     * @param $id
     * @return object
     *
     * Возвращает полную плоскую запись элемента справочника stdClass
     */
    public function getRecord($id)
    {
        if( isset($this->base[$id]) ){
            $record = $this->base[$id];
            $record['id'] = $id;
            return (object) $record;
        }else{
            return null;
        }
    }


    /**
     * Возвращаем весь справочник
     * @return array
     */
    public function getBase()
    {
        return $this->base;
    }


    /**
     * @param null $parrent_id - доп. фильтр по зависимости
     * @return mixed
     *
     * Возвращает ID первого значения справочника
     * (Значение по умолчанию)
     */
    public function getDefault($parrent_id = null)
    {
        if($parrent_id == null) {
            return key($this->base);
        }else{
            return key( ArrayHelper::getColumn($this->findAll($parrent_id), 'id') );
        }
    }


    /**
     * @param $field
     * @param null $id
     * @return array|null
     * @throws MethodNotAllowedHttpException
     *
     * Возвращаем дополнительное поле Элемента справочника по ИД либо список всех доп.полей справочника
     */
    private function getField($field, $id = null, $relation_id = null, $ext_where = null)
    {
        if( isset($this->struct['fields'][$field]) ){

            if($id == null){
                return ArrayHelper::getColumn($this->findAll($relation_id, $ext_where), 'ext_' . $field );
            }elseif( isset($this->base[$id]) ){
                return $this->base[$id]['ext_' . $field];
            }else{
                return null;
            }
        }else{
            throw new MethodNotAllowedHttpException('Extra field ['.$field.'] in klbase:'.$this->struct['name']. ' not found');
        }
    }


    /**
     * @param mixed $relation_id  Критерий ИД зависимости
     * @param mixed $ext_where    Критерий значения доп полей  array($extFieldName => $extFieldValue)
     * @return array
     *
     * Фильтрация справочника по критериям
     */
    public function findAll($relation_id = null, $ext_where = null)
    {
        $result = $this->base;

        if($relation_id != null){
            // Фильтруем по условию, зависимый справочник
            foreach ($result as $key => $item){
                if($item['relation'] != $relation_id){
                    unset($result[$key]);
                }
            }
        }

        if($ext_where != null){
            // Фильтруем по условиям доп. полей
            foreach ($result as $key => $item){
                foreach ($ext_where as $name => $value) {
                    if($item['ext_' . $name] !=  $value){
                        unset($result[$key]);
                    }
                }
            }
        }

        return $result;
    }









}