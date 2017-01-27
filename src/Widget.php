<?php
/**
 * Google ReCaptcha v 2.0 Yii 2.x.x extension widget.
 * Class Widget
 * @author Brusenskiy Dmitry <brussens@nativeweb.ru>
 * @since 1.0.0
 * @version 1.0.0
 * @link https://github.com/brussens/yii2-recaptcha <Repostory>
 * @copyright 2017 Brusenskiy Dmitry
 * @license http://opensource.org/licenses/MIT MIT
 * @package brussens\yii2\extensions\recaptcha
 */
namespace brussens\yii2\extensions\recaptcha;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\InputWidget;
use yii\helpers\Json;
class Widget extends InputWidget
{
    /**
     * Except languages.
     */
    const EXCEPT = ['zh-HK','zh-CN','zh-TW','en-GB','fr-CA','de-AT','de-CH','pt-BR','pt-PT','es-419'];
    /**
     * Options of JS script.
     * @see https://developers.google.com/recaptcha/docs/display#js_api
     * @var array
     */
    public $clientOptions = [];
    /**
     * Flag of rendering noscript section.
     * @var bool
     */
    public $renderNoScript = true;
    /**
     * @inheritdoc
     */
    public function run()
    {
        parent::run();
        $this->registerScripts();

        echo $this->hasModel() ?
        Html::activeHiddenInput($this->model, $this->attribute, $this->options) :
        Html::hiddenInput($this->name, $this->value, $this->options);

        echo Html::tag('div', null, [
            'id' => $this->options['id'] . '-recaptcha-container'
        ]);
        if($this->renderNoScript) {
            $this->renderNoScript();
        }
    }
    /**
     * Registration client scripts.
     */
    protected function registerScripts()
    {
        $view = $this->getView();
        $view->registerJsFile(
            "//www.google.com/recaptcha/api.js?hl=" . $this->getLanguagePrefix(),
            ['position' => $view::POS_HEAD, 'async' => true, 'defer' => true]
        );

        $options = ArrayHelper::merge(['sitekey' => Yii::$app->recaptcha->siteKey], $this->clientOptions);
        $view->registerJs(
            'grecaptcha.render("' . $this->options['id'] . '-recaptcha-container", ' . Json::encode($options) . ');',
            $view::POS_LOAD
        );
    }
    /**
     * Rendering noscript section.
     */
    protected function renderNoScript()
    {
        echo Html::beginTag('noscript');
        echo Html::tag('iframe', null, [
            'src' => 'https://www.google.com/recaptcha/api/fallback?k=' . Yii::$app->recaptcha->siteKey,
            'frameborder' => 0,
            'width' => '302px',
            'height' => '423px',
            'scrolling' => 'no',
            'border-style' => 'none'
        ]);
        echo Html::textarea('g-recaptcha-response', null, [
            'class' => 'form-control',
            'style' => 'margin-top: 15px'
        ]);
        echo Html::endTag('noscript');
    }
    /**
     * Normalize language code.
     * @return string
     */
    protected function getLanguagePrefix()
    {
        $language = Yii::$app->language;
        if(!in_array($language, self::EXCEPT) && preg_match('/[a-z]+-[A-Z0-9]+/', $language)) {
            $language = explode('-', $language)[0];
        }
        return $language;
    }
}