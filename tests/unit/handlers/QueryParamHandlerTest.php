<?php

namespace cetver\LanguagesDispatcher\tests\handlers;

use cetver\LanguagesDispatcher\handlers\QueryParamHandler;
use cetver\LanguagesDispatcher\tests\AbstractUnitTest;
use Yii;
use yii\base\InvalidConfigException;
use yii\web\Request;

class QueryParamHandlerTest extends AbstractUnitTest
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
                new QueryParamHandler([
                    'request' => $request,
                ]);
            }
        );

        $this->mockWebApplication();
        $handler = new QueryParamHandler();
        $this->tester->assertInstanceOf(Request::className(), $handler->request);
    }

    public function testGetLanguages()
    {
        $this->mockWebApplication();
        $handler = new QueryParamHandler();
        $this->tester->assertSame([null], $handler->getLanguages());

        $this->mockWebApplication();
        Yii::$app->getRequest()->setQueryParams(['language' => 'ru']);
        $handler = new QueryParamHandler();
        $this->tester->assertSame(['ru'], $handler->getLanguages());

        $this->mockWebApplication();
        Yii::$app->getRequest()->setQueryParams(['lang' => 'en']);
        $handler = new QueryParamHandler(['queryParam' => 'lang']);
        $this->tester->assertSame(['en'], $handler->getLanguages());
    }
}