<?php
/**
 * @link https://github.com/brussens/yii2-recaptcha
 * @copyright Copyright © since 2017 Brusensky Dmitry. All rights reserved
 * @licence http://opensource.org/licenses/MIT MIT
 */

namespace brussens\yii2\extensions\recaptcha;

use Yii;
use ReCaptcha\ReCaptcha;

/**
 * Google ReCaptcha v 2.0 Yii 2.x.x extension validator.
 * @package brussens\yii2\extensions\recaptcha
 * @author Brusensky Dmitry <brussens@nativeweb.ru>
 */
class Validator extends \yii\validators\Validator
{
    /**
     * ReCaptcha object.
     * @var ReCaptcha
     */
    private $reCaptcha;

    /**
     * Validator constructor.
     * @param ReCaptcha $reCaptcha
     * @param array $config
     */
    public function __construct(ReCaptcha $reCaptcha, $config = [])
    {
        $this->reCaptcha = $reCaptcha;

        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if (!$this->message) {
            $this->message = Yii::t('yii', 'The verification code is incorrect.');
        }
    }

    /**
     * @inheritdoc
     */
    protected function validateValue($value)
    {
        $request = Yii::$app->getRequest();
        $value = $request->post('g-recaptcha-response');
        if(!$value) {
            return [Yii::t('yii', '{attribute} cannot be blank.'), []];
        }
        $response = $this->reCaptcha->verify($value, Yii::$app->getRequest()->getUserIP());
        return $response->isSuccess() ? null : [$this->message, []];
    }

    /**
     * @param \yii\base\Model $model
     * @param string $attribute
     * @param \yii\web\View $view
     * @return string
     */
    public function clientValidateAttribute($model, $attribute, $view)
    {
        $message = Yii::t('yii', '{attribute} cannot be blank.', [
            'attribute' => $model->getAttributeLabel($attribute)
        ]);

        return "(function(messages){if(!grecaptcha.getResponse()){messages.push('{$message}');}})(messages);";
    }
}