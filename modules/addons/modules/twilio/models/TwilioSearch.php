<?php
/**
 * Copyright (C) Baluart.COM - All Rights Reserved
 *
 * @since 1.0
 * @author Baluart E.I.R.L.
 * @copyright Copyright (c) 2015 - 2021 Baluart E.I.R.L.
 * @license http://codecanyon.net/licenses/faq Envato marketplace licenses
 * @link https://easyforms.dev/ Easy Forms
 */

namespace app\modules\addons\modules\twilio\models;

use app\components\User;
use app\helpers\ArrayHelper;
use Yii;
use yii\data\ActiveDataProvider;

/**
 * TwilioSearch represents the model behind the search form about `app\modules\addons\modules\twilio\models\Sms`.
 */
class TwilioSearch extends Twilio
{

    public $form;
    public $lastEditor;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'form_id', 'status', 'event'], 'integer'],
            [['name', 'form', 'lastEditor'], 'string'],
            [['name', 'url', 'to', 'from', 'text', 'form', 'lastEditor', 'updated_at'], 'safe'],
        ];
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Twilio::find();

        $query->joinWith(['form', 'lastEditor']);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => Yii::$app->user->preferences->get('GridView.pagination.pageSize'),
            ],
            'sort' => [
                'defaultOrder' => [
                    'updated_at' => SORT_DESC,
                ]
            ],
        ]);

        $dataProvider->sort->attributes['form'] = [
            'asc' => ['{{%form}}.name' => SORT_ASC],
            'desc' => ['{{%form}}.name' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['lastEditor'] = [
            'asc' => ['{{%user}}.username' => SORT_ASC],
            'desc' => ['{{%user}}.username' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        if (isset($this->updated_at) && !empty($this->updated_at)) {
            list($start, $end) = explode(" - ", $this->updated_at);
            $startAt = strtotime(trim($start));
            // Add +1 day to the endAt
            $endAt = strtotime(trim($end)) + (24 * 60 * 60);
            $query->andFilterWhere(['between', '{{%addon_twilio}}.updated_at', $startAt, $endAt]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'form_id' => $this->form_id,
            '{{%addon_twilio}}.status' => $this->status,
            '{{%addon_twilio}}.event' => $this->event
        ]);

        $query
            ->andFilterWhere(['like', '{{%addon_twilio}}.name', $this->name])
            ->andFilterWhere(['like', '{{%form}}.name', $this->form])
            ->andFilterWhere(['like', '{{%user}}.username', $this->lastEditor]);

        /** @var User $currentUser */
        $currentUser = Yii::$app->user;

        // If current user can see all forms, return data provider
        if ($currentUser->can("viewForms")) {
            return $dataProvider;
        }

        $forms = $currentUser->forms()->asArray()->all();
        $formIds = ArrayHelper::getColumn($forms, 'id');

        // Important restriction. If empty, don't show any configuration
        $formIds = count($formIds) > 0 ? $formIds : 0;
        $query->andFilterWhere(['{{%addon_twilio}}.form_id' => $formIds]);

        return $dataProvider;
    }
}