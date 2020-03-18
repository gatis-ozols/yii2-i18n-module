<?php

namespace Zelenin\yii\modules\I18n\models\query;

use Yii;
use yii\db\ActiveQuery;
use Zelenin\yii\modules\I18n\models\Message;
use Zelenin\yii\modules\I18n\models\SourceMessage;

class SourceMessageQuery extends ActiveQuery
{
    protected static function translatedMessageIdsQuery()
    {
        $messageTableName = Message::tableName();
        $query = Message::find()->select($messageTableName . '.id');
        $i = 0;
        foreach (Yii::$app->getI18n()->languages as $language) {
            if ($i === 0) {
                $query->andWhere($messageTableName . '.language = :language' . $i . ' and ' . $messageTableName . '.translation is not null and ' . $messageTableName . '.translation != \'\'', [':language'.$i => $language]);
            } else {
                $query->innerJoin($messageTableName . ' t' . $i, 't' . $i . '.id = ' . $messageTableName . '.id and t' . $i . '.language = :language' . $i . ' and t' . $i . '.translation is not null and t' . $i . '.translation != \'\'', [':language'.$i => $language]);
            }
            $i++;
        }
        return $query;
    }

    public function notTranslated()
    {
        $query = self::translatedMessageIdsQuery();
        $this->andWhere(['not in', SourceMessage::tableName().'.id', $query]);
        return $this;
    }

    public function translated()
    {
        $query = self::translatedMessageIdsQuery();
        $this->andWhere(['in', SourceMessage::tableName().'.id', $query]);
        return $this;
    }
}
