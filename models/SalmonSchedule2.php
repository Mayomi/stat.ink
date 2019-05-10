<?php
/**
 * @copyright Copyright (C) 2015-2019 AIZAWA Hina
 * @license https://github.com/fetus-hina/stat.ink/blob/master/LICENSE MIT
 * @author AIZAWA Hina <hina@bouhime.com>
 */

namespace app\models;

use Yii;
use app\components\helpers\db\Now;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "salmon_schedule2".
 *
 * @property integer $id
 * @property integer $map_id
 * @property string $start_at
 * @property string $end_at
 *
 * @property SalmonMap2 $map
 * @property SalmonWeapon2[] $weapons
 */
class SalmonSchedule2 extends ActiveRecord
{
    public static function find(): ActiveQuery
    {
        return new class(static::class) extends ActiveQuery {
            public function init()
            {
                parent::init();
                $this->orderBy([
                    '{{salmon_schedule2}}.[[start_at]]' => SORT_ASC,
                ]);
            }

            public function nowOrFuture(): self
            {
                $this->andWhere(['and',
                    ['>=', '{{salmon_schedule2}}.[[end_at]]', new Now()],
                ]);
                return $this;
            }
        };
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'salmon_schedule2';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['map_id', 'start_at', 'end_at'], 'required'],
            [['map_id'], 'default', 'value' => null],
            [['map_id'], 'integer'],
            [['start_at', 'end_at'], 'safe'],
            [['map_id'], 'exist', 'skipOnError' => true,
                'targetClass' => SalmonMap2::class,
                'targetAttribute' => ['map_id' => 'id'],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'map_id' => 'Map ID',
            'start_at' => 'Start At',
            'end_at' => 'End At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMap()
    {
        return $this->hasOne(SalmonMap2::class, ['id' => 'map_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWeapons()
    {
        return $this->hasMany(SalmonWeapon2::class, ['schedule_id' => 'id'])
            ->orderBy(['id' => SORT_ASC]);
    }

    public function delete()
    {
        return $this->db->transactionEx(function (): bool {
            foreach ($this->weapons as $weapon) {
                if (!$weapon->delete()) {
                    return false;
                }
            }
            return parent::delete();
        });
    }
}
