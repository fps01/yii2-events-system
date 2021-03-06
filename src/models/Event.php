<?php

namespace DevGroup\EventsSystem\models;

use DevGroup\EventsSystem\helpers\EventHelper;
use DevGroup\EventsSystem\traits\ListData;
use DevGroup\TagDependencyHelper\TagDependencyTrait;

/**
 * This is the model class for table "{{%devgroup_event}}".
 *
 * @property integer $id
 * @property integer $event_group_id
 * @property string $name
 * @property string $description
 * @property string $event_class_name
 * @property string $execution_point
 */
class Event extends \yii\db\ActiveRecord
{
    use TagDependencyTrait;
    use ListData;

    public function behaviors()
    {
        return [
            'tagDependency' => [
                'class' => 'DevGroup\TagDependencyHelper\CacheableActiveRecord',
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%devgroup_event}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['event_group_id', 'name', 'execution_point'], 'required'],
            [['event_group_id'], 'exist', 'targetClass' => EventGroup::className(), 'targetAttribute' => 'id'],
            [['description'], 'string'],
            [['name'], 'string', 'max' => 100],
            [['event_class_name', 'execution_point'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => EventHelper::t('ID'),
            'event_group_id' => EventHelper::t('Event group'),
            'name' => EventHelper::t('Name'),
            'description' => EventHelper::t('Description'),
            'event_class_name' => EventHelper::t('Event class name'),
            'execution_point' => EventHelper::t('Execution point'),
        ];
    }

    /**
     * @return array
     */
    public static function dropDownListWithGroup()
    {
        EventGroup::preloadData();
        return static::dropDownList(function ($model, $default) {
            return $model['name'] . ' (' . EventGroup::getNameById($model['event_group_id']) . ')';
        });
    }

    /**
     * @param null $eventGroupId
     * @return array
     */
    public static function dropDownListForGroup($eventGroupId = null)
    {
        if ($eventGroupId === null) {
            return static::dropDownListWithGroup();
        }
        return static::find()
            ->select(['name', 'id'])
            ->where(['event_group_id' => $eventGroupId])
            ->indexBy('id')
            ->column();
    }
}
