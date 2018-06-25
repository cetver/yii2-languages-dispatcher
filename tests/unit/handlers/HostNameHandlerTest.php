<?php

namespace cetver\LanguagesDispatcher\tests\unit\handlers;

use cetver\LanguagesDispatcher\handlers\HostNameHandler;
use cetver\LanguagesDispatcher\tests\AbstractUnitTest;
use yii\base\InvalidConfigException;
use yii\web\Request;

/**
 * Class HostHandlerTest
 *
 * @package cetver\LanguagesDispatcher\tests\unit\handlers
 */
class HostNameHandlerTest extends AbstractUnitTest
{
    public function testInit()
    {
        $request = 'invalid-request';
        $invalidConfigExceptionClassName = ((new \ReflectionClass(new InvalidConfigException()))->getName());

        $this->tester->expectException($invalidConfigExceptionClassName, function () use ($request) {
            $this->mockWebApplication();
            new HostNameHandler([
                'request' => $request,
            ]);
        });

        $this->tester->expectException($invalidConfigExceptionClassName, function () {
            $this->mockWebApplication();
            new HostNameHandler([
                'hostMap' => 'whatever'
            ]);
        });

        $handler = new HostNameHandler();
        $this->tester->assertInstanceOf(Request::className(), $handler->request);
    }

    public function testGetLanguages()
    {
        $this->mockWebApplication();


        $handler = new HostNameHandler([
            'hostMap' => [
                'ru.example.com'   => 'ru',
            ]
        ]);

        $handler->request->setHostInfo('http://uk.example.com');
        $this->tester->assertSame([], $handler->getLanguages());

        $handler->request->setHostInfo('https://ru.example.com');
        $this->tester->assertSame(['ru'], $handler->getLanguages());
    }
}