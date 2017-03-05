<?php


namespace brussens\yii2\extensions\recaptcha\test;


use brussens\yii2\extensions\recaptcha\Validator;
use ReCaptcha\ReCaptcha;
use ReCaptcha\Response;
use yii\web\Application;
use yii\web\Request;

class ValidatorTest extends TestCase
{

    /**
     * @var Application
     */
    private $application;

    /**
     * @var string
     */
    private $userIp = '127.0.0.1';

    /**
     * @var string
     */
    private $gReCaptchaResponse = 'test captcha response';

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|ReCaptcha
     */
    private $reCaptchaStub;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Request
     */
    private $requestStub;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject|Response
     */
    private $reCaptchaResponseStub;


    protected function setUp()
    {
        $application = $this->mockWebApplication([]);
        $reCaptchaResponseStub = $this->createMock(Response::class);

        $reCaptchaStub = $this->createMock(ReCaptcha::class);
        $reCaptchaStub->method('verify')->willReturn($reCaptchaResponseStub);

        /** @var ReCaptcha $reCaptchaStub */

        $requestStub = $this->createMock(Request::class);
        $requestStub->method('getUserIp')->willReturn($this->userIp);

        /** @var Request $validator */

        $application->set('request', $requestStub);

        $this->application = $application;
        $this->reCaptchaStub = $reCaptchaStub;
        $this->requestStub = $requestStub;
        $this->reCaptchaResponseStub = $reCaptchaResponseStub;

    }


    public function testValidationSucceed()
    {

        $validator = new Validator($this->reCaptchaStub);

        $this->requestStub->method('post')->willReturn($this->gReCaptchaResponse);

        $this->reCaptchaResponseStub->method('isSuccess')->willReturn(true);

        $this->assertTrue($validator->validate($this->gReCaptchaResponse));

    }


    public function testValidationFail()
    {
        $validator = new Validator($this->reCaptchaStub);

        $this->assertFalse($validator->validate($this->gReCaptchaResponse));

        $this->requestStub->method('post')->willReturn($this->gReCaptchaResponse);
        $this->reCaptchaResponseStub->method('isSuccess')->willReturn(false);

        $this->assertFalse($validator->validate($this->gReCaptchaResponse));
    }


    public function testValidationCall()
    {

        $responseStub = $this->createMock(Response::class);
        $reCaptchaMock = $this->createMock(ReCaptcha::class);
        $reCaptchaMock->expects($this->once())->method('verify')->with($this->equalTo($this->gReCaptchaResponse), $this->equalTo($this->userIp))->willReturn($responseStub);

        /** @var ReCaptcha $reCaptchaMock */

        $validator = new Validator($reCaptchaMock);

        $this->requestStub->method('post')->willReturn($this->gReCaptchaResponse);

        $validator->validate($this->gReCaptchaResponse);

    }


}