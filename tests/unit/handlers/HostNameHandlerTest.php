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
        $this->tester->expectException(
            new InvalidConfigException(sprintf(
                'The component with the specified ID "%s" must be an instance of "%s"',
                $request,
                Request::className()
            )),
            function () use ($request) {
                $this->mockWebApplication();
                new HostNameHandler([
                    'request' => $request,
                ]);
            }
        );

        $hostMapConfigException = new InvalidConfigException(
            'The "hostMap" property must be an array or callable function that returns an array'
        );
        $this->tester->expectException($hostMapConfigException, function () {
            $this->mockWebApplication();
            new HostNameHandler([
                'hostMap' => 'non-array',
            ]);
        });

        $this->tester->expectException($hostMapConfigException, function () {
            $this->mockWebApplication();
            new HostNameHandler([
                'hostMap' => function () {
                    return false;
                }
            ]);
        });

        $handler = new HostNameHandler([
            'hostMap' => function () {
                return [
                    'ru.example.com' => 'ru'
                ];
            }
        ]);
        $this->tester->assertArrayHasKey('ru.example.com', $handler->hostMap);
        $this->tester->assertInstanceOf(Request::className(), $handler->request);
    }

    public function testGetLanguages()
    {
        $hostMap = [
            'ru.example.com' => 'ru',
            'cn.example.com' => 'cn'
        ];

        $this->mockWebApplication();
        $handler = new HostNameHandler([
            'hostMap' => $hostMap
        ]);

        $handler->request->setHostInfo('http://uk.example.com');
        $this->tester->assertSame([], $handler->getLanguages());

        $handler->request->setHostInfo('https://ru.example.com');
        $this->tester->assertSame(['ru'], $handler->getLanguages());

        $this->mockWebApplication();
        $handler = new HostNameHandler([
            'hostMap' => function () use ($hostMap) {
                return $hostMap;
            }
        ]);

        $handler->request->setHostInfo('http://ro.example.com');
        $this->tester->assertSame([], $handler->getLanguages());

        $handler->request->setHostInfo('https://cn.example.com');
        $this->tester->assertSame(['cn'], $handler->getLanguages());
    }
}