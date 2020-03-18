<?php

namespace Zelenin\yii\modules\I18n\models\search;

use yii\data\ActiveDataProvider;
use Yii;
use yii\helpers\ArrayHelper;
use Zelenin\yii\modules\I18n\models\SourceMessage;
use Zelenin\yii\modules\I18n\models\Message;
use Zelenin\yii\modules\I18n\Module;

class SourceMessageSearch extends SourceMessage
{
    const STATUS_TRANSLATED = 1;
    const STATUS_NOT_TRANSLATED = 2;

    public $status;

    protected static $dynamicFields;
    protected static $dynamicLabels;

    public static function getHasFieldAlias($language)
    {
        return 'has_'.$language;
    }

    protected static function getMessageTableAlias($language)
    {
        return 'm_'.$language;
    }

    public static function getDynamicFields()
    {
        if(!isset(self::$dynamicFields)) {
            self::$dynamicFields = [];
            foreach (Yii::$app->getI18n()->languages as $language) {
                self::$dynamicFields[] = self::getHasFieldAlias($language);
            }
        }
        return self::$dynamicFields;
    }

    public static function getDynamicLabels()
    {
        if(!isset(self::$dynamicLabels)) {
            self::$dynamicLabels = [];
            foreach (Yii::$app->getI18n()->languages as $language) {
                self::$dynamicLabels[self::getHasFieldAlias($language)] = $language;
            }
        }
        return self::$dynamicLabels;
    }

    /**
     * @inheritdoc
     */
    public function attributes()
    {
        return ArrayHelper::merge(parent::attributes(), self::getDynamicFields());
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), self::getDynamicLabels());
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        $rules = [
            ['category', 'safe'],
            ['message', 'safe'],
            ['status', 'safe']
        ];

        $rules[] = [self::getDynamicFields(), 'safe'];
        $rules[] = [self::getDynamicFields(), 'boolean'];

        return $rules;
    }

    /**
     * @param array|null $params
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = self::find()->with('messages');

        $messageTableName = Message::tableName();

        $query->select(self::tableName() . '.*');
        
        $isValid = ($this->load($params) && $this->validate());

        $i = 0;
        foreach (Yii::$app->getI18n()->languages as $language) {
            $alias = self::getMessageTableAlias($language);
            $fieldAlias = self::getHasFieldAlias($language);

            $query->leftJoin($messageTableName . ' ' . $alias, $alias . '.id = ' . self::tableName() . '.id and ' . $alias . '.language = :language'.$i, [':language'.$i => $language]);
            $query->addSelect([new \yii\db\Expression('CASE WHEN ' . $alias . '.translation IS NOT NULL AND ' . $alias . '.translation != \'\' THEN true ELSE false END AS `' . $fieldAlias . '`')]);

            if($isValid) {
                $val = $this->getAttribute($fieldAlias);
                if($val !== '') {
                    if($val === '1') {
                        $query->andWhere(['not', [$alias . '.translation' => null]]);
                        $query->andWhere(['not', [$alias . '.translation' => '']]);
                    }
                    else {
                        $query->andWhere(['or',
                            [$alias . '.translation' => null],
                            [$alias . '.translation' => '']
                        ]);
                    }
                }
            }

            $i++;
        }

        $dataProvider = new ActiveDataProvider(['query' => $query]);

        if (!$isValid) {
            return $dataProvider;
        }

        if ($this->status == static::STATUS_TRANSLATED) {
            $query->translated();
        }
        if ($this->status == static::STATUS_NOT_TRANSLATED) {
            $query->notTranslated();
        }

        $query
            ->andFilterWhere(['=', 'category', $this->category]);
        if($this->message !== '' && trim($this->message) !== '') {
            $subquery = Message::find()->select('id')->where(['like', 'translation', $this->message]);
            $query->andWhere([
                'or',
                ['like', 'message', $this->message],
                ['in', self::tableName().'.id', $subquery]
            ]);
        }
        return $dataProvider;
    }

    public static function getStatus($id = null)
    {
        $statuses = [
            self::STATUS_TRANSLATED => Module::t('Translated'),
            self::STATUS_NOT_TRANSLATED => Module::t('Not translated'),
        ];
        if ($id !== null) {
            return ArrayHelper::getValue($statuses, $id, null);
        }
        return $statuses;
    }
}
