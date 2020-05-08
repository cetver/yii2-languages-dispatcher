<?php

namespace cetver\LanguagesDispatcher\tests\handlers;

use cetver\LanguagesDispatcher\handlers\AcceptLanguageHeaderHandler;
use cetver\LanguagesDispatcher\tests\AbstractUnitTest;
use yii\base\InvalidConfigException;
use yii\web\Request;

class AcceptLanguageHeaderHandlerTest extends AbstractUnitTest
{
    public function testInit()
    {
        $request = 'invalid-request';
        $this->tester->expectException(
            new InvalidConfigException(sprintf(
                'The component with the specified ID "%s" must be an instance of "%s"',
                $request,
                Request::className()
            )),
            function () use ($request) {
                $this->mockWebApplication();
                new AcceptLanguageHeaderHandler([
                    'request' => $request,
                ]);
            }
        );

        $this->mockWebApplication();
        $handler = new AcceptLanguageHeaderHandler();
        $this->tester->assertInstanceOf(Request::className(), $handler->request);
    }

    public function testGetLanguages()
    {
        $this->mockWebApplication();
        $handler = new AcceptLanguageHeaderHandler();
        $this->tester->assertSame([], $handler->getLanguages());

        $this->mockWebApplication();
        \Yii::$app->getRequest()->getHeaders()->add('Accept-Language', 'ru,en-GB;q=0.8,en-US;q=0.6,en;q=0.4');
        $handler = new AcceptLanguageHeaderHandler();
        $this->tester->assertSame(['ru', 'en-GB', 'en-US', 'en'], $handler->getLanguages());
    }
}