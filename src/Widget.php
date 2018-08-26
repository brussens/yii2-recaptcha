<?php
/**
 * @link https://github.com/brussens/yii2-recaptcha
 * @copyright Copyright © since 2017 Brusensky Dmitry. All rights reserved
 * @licence http://opensource.org/licenses/MIT MIT
 */

namespace brussens\yii2\extensions\recaptcha;

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\InputWidget;
use yii\helpers\Json;

/**
 * Google ReCaptcha v 2.0 Yii 2.x.x extension widget.
 * @package brussens\yii2\extensions\recaptcha
 * @author Brusensky Dmitry <brussens@nativeweb.ru>
 */
class Widget extends InputWidget
{
    /**
     * Flag of rendering noscript section.
     * @var bool
     */
    public $renderNoScript = true;
    /**
     * JavaScript options.
     * @see https://developers.google.com/recaptcha/docs/display#js_api
     * @var array
     */
    public $clientOptions = [];
    /**
     * noscript > iframe tag options.
     * @var array
     */
    public $noScriptIFrameOptions = [];
    /**
     * noscript > textarea tag options.
     * @var array
     */
    public $noScriptTextAreaOptions = [];
    /**
     * @var string
     */
    private $siteKey;
    /**
     * @var string
     */
    private $language;

    /**
     * Widget constructor.
     * @param string $siteKey
     * @param string $language
     * @param array $config
     */
    public function __construct($siteKey, $language, $config = [])
    {
        $this->siteKey  = $siteKey;
        $this->language = $language;

        parent::__construct($config);
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        parent::run();

        $this->registerScripts();

        $output = $this->hasModel() ?
            Html::activeHiddenInput($this->model, $this->attribute, $this->options) :
            Html::hiddenInput($this->name, $this->value, $this->options);

        $output .= Html::tag('div', null, [
            'id' => $this->options['id'] . '-recaptcha-container'
        ]);

        if($this->renderNoScript) {
            $output .= $this->renderNoScript();
        }

        return $output;
    }

    /**
     * Registration client scripts.
     */
    private function registerScripts()
    {
        $view = $this->getView();
        $view->registerJsFile(
            "//www.google.com/recaptcha/api.js?hl=" . $this->getLanguagePrefix(),
            ['position' => $view::POS_HEAD, 'async' => true, 'defer' => true]
        );

        $options = ArrayHelper::merge(['sitekey' => $this->siteKey], $this->clientOptions);
        $view->registerJs(
            'grecaptcha.render("' . $this->options['id'] . '-recaptcha-container", ' . Json::encode($options) . ');',
            $view::POS_LOAD
        );
    }

    /**
     * Rendering noscript section.
     * @return string
     */
    protected function renderNoScript()
    {
        $iFrameOptions = ArrayHelper::merge([
                'frameborder' => 0,
                'width' => '302px',
                'height' => '423px',
                'scrolling' => 'no',
                'border-style' => 'none'
        ], $this->noScriptIFrameOptions);
        $iFrameOptions['src'] = 'https://www.google.com/recaptcha/api/fallback?k=' . $this->siteKey;

        $textAreaOptions = ArrayHelper::merge([
            'class' => 'form-control',
            'style' => 'margin-top: 15px'
        ], $this->noScriptTextAreaOptions);

        $output = Html::beginTag('noscript');
        $output .= Html::tag('iframe', null, $iFrameOptions);
        $output .= Html::textarea('g-recaptcha-response', null, $textAreaOptions);
        $output .= Html::endTag('noscript');

        return $output;
    }

    /**
     * Normalize language code.
     * @return string
     */
    protected function getLanguagePrefix()
    {
        $language = $this->language;
        $except = ['zh-HK','zh-CN','zh-TW','en-GB','fr-CA','de-AT','de-CH','pt-BR','pt-PT','es-419'];
        if(!in_array($language, $except) && preg_match('/[a-z]+-[A-Z0-9]+/', $language)) {
            $language = explode('-', $language)[0];
        }
        return $language;
    }
}