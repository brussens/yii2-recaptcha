<?php


namespace brussens\yii2\extensions\recaptcha\test;


use brussens\yii2\extensions\recaptcha\Widget;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\web\View;

class WidgetTest extends TestCase
{

    protected function setUp()
    {
        $application = $this->mockWebApplication();
        $viewStub = $this->createMock(View::class);
        $application->set('view', $viewStub);
    }


    public function testRegisterScripts()
    {

        $siteKey = 'testKey';

        $widget = new Widget($siteKey, 'ru-RU', ['name' => 'reCaptcha']);

        $viewMock = $this->createMock(View::class);

        $viewMock->expects($this->once())->method('registerJsFile')->with(
            $this->equalTo("//www.google.com/recaptcha/api.js?hl=ru"),
            $this->equalTo(['position' => View::POS_HEAD, 'async' => true, 'defer' => true])
        );

        $options = ArrayHelper::merge(['sitekey' => $siteKey], $widget->clientOptions);
        $viewMock->expects($this->once())->method('registerJs')->with(
            'grecaptcha.render("' . $widget->options['id'] . '-recaptcha-container", ' . Json::encode($options) . ');',
            View::POS_LOAD
        );

        /** @var View $viewMock */

        $widget->setView($viewMock);

        $widget->run();

    }


    public function testWithModel()
    {

        $model = new TestModel();
        $attribute = 'verifyCode';

        $expectedOutput = $this->renderWidgetWithModel($model, $attribute, ['id' => Html::getInputId($model, $attribute)]);

        $widget = new Widget('testKey', 'ru-RU', ['model' => $model, 'attribute'=> $attribute, 'renderNoScript' => false]);

        $this->assertEquals($expectedOutput, $widget->run());

    }


    public function testWithoutModel()
    {

        $name = 'verifyCode';

        $value = 'testValue';

        $options = [
            'id' => 'reCaptcha',
            'name' => $name,
            'renderNoScript' => false,
            'value' => $value
        ];

        $expectedOutput = $this->renderWidgetWithoutModel($name, $value, $options);

        $widget = new Widget('testKey', 'ru-RU', $options);

        $this->assertEquals($expectedOutput, $widget->run());

    }


    public function testRenderNoScript()
    {

        $model = new TestModel();

        $attribute = 'verifyCode';

        $siteKey = 'testKey';

        $expectedOutput = $this->renderWidgetWithModel($model, $attribute, ['id' => Html::getInputId($model, $attribute)]) . $this->renderNoScript($siteKey);

        $widget = new Widget($siteKey, 'ru-RU', ['model' => $model, 'attribute'=> $attribute]);

        $this->assertEquals($expectedOutput, $widget->run());

    }


    /**
     * Rendering noscript section.
     *
     * @param string $siteKey
     *
     * @return string
     */
    private function renderNoScript($siteKey)
    {

        $output = Html::beginTag('noscript');
        $output .= Html::tag('iframe', null, [
            'src' => 'https://www.google.com/recaptcha/api/fallback?k=' . $siteKey,
            'frameborder' => 0,
            'width' => '302px',
            'height' => '423px',
            'scrolling' => 'no',
            'border-style' => 'none'
        ]);
        $output .= Html::textarea('g-recaptcha-response', null, [
            'class' => 'form-control',
            'style' => 'margin-top: 15px'
        ]);
        $output .= Html::endTag('noscript');
        return $output;
    }


    private function renderWidgetWithModel(Model $model, $attribute, array $options)
    {

        $output = Html::activeHiddenInput($model, $attribute, $options);

        $output .= Html::tag('div', null, [
            'id' => $options['id'] . '-recaptcha-container'
        ]);

        return $output;
    }


    private function renderWidgetWithoutModel($name, $value, array $options)
    {

        $output = Html::hiddenInput($name, $value, $options);

        $output .= Html::tag('div', null, [
            'id' => $options['id'] . '-recaptcha-container'
        ]);

        return $output;

    }


}