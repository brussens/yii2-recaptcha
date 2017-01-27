#Google ReCaptcha v2.0 Yii 2.x.x extension
[![Latest Stable Version](https://poser.pugx.org/brussens/yii2-recaptcha/v/stable)](https://packagist.org/packages/brussens/yii2-recaptcha)
[![Total Downloads](https://poser.pugx.org/brussens/yii2-recaptcha/downloads)](https://packagist.org/packages/brussens/yii2-recaptcha)
[![License](https://poser.pugx.org/brussens/yii2-recaptcha/license)](https://packagist.org/packages/brussens/yii2-recaptcha)
##Install
Either run
```
php composer.phar require --prefer-dist brussens/yii2-recaptcha "*"
```

or add

```
"brussens/yii2-recaptcha": "*"
```

to the require section of your `composer.json` file.

Add to your config file:
```php
'components' => [
    ...
    'recaptcha' => [
        'class' => 'brussens\\yii2\\extensions\\recaptcha\\Component',
        'siteKey' => '!!!Insert your public key here!!!',
        'secretKey' => '!!!Insert your secret key here!!!'
    ],
    ...
],
```

Add in your model validation rules
```php
public function rules()
{
    return [
        ...
        ['verifyCode', \brussens\yii2\extensions\recaptcha\Validator::className()],
        ...
    ];
}
```

Add in your view
```php
echo $form->field($model, 'verifyCode')->widget(\brussens\yii2\extensions\recaptcha\Widget::className());
```

If you use Pjax or multiple widgets on page
```php
echo $form->field($model, 'verifyCode')->widget(
    \brussens\yii2\extensions\recaptcha\Widget::className(), [
    'options' => [
        'id' => 'insert-unique-widget-id'
    ]
]);
```