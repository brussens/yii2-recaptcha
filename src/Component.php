<?php
/**
 * Google ReCaptcha v 2.0 Yii 2.x.x extension component.
 * Class Component
 * @author Brusenskiy Dmitry <brussens@nativeweb.ru>
 * @since 1.0.0
 * @version 1.0.0
 * @link https://github.com/brussens/yii2-recaptcha <Repostory>
 * @copyright 2017 Brusenskiy Dmitry
 * @license http://opensource.org/licenses/MIT MIT
 * @package brussens\yii2\extensions\recaptcha
 */
namespace brussens\yii2\extensions\recaptcha;
use yii\base\InvalidConfigException;

class Component extends \yii\base\Component
{
    /**
     * Recaptcha site secret key.
     * @var
     */
    public $secretKey;
    /**
     * Recaptcha site public key.
     * @var
     */
    public $siteKey;
    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        if(!$this->secretKey) {
            throw new InvalidConfigException('Required `secretKey` param isn\'t set .');
        }
        if(!$this->siteKey) {
            throw new InvalidConfigException('Required `siteKey` param isn\'t set .');
        }
    }
}