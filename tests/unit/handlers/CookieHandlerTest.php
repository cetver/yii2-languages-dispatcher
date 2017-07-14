<?php

namespace cetver\LanguagesDispatcher\tests\handlers;

use cetver\LanguagesDispatcher\handlers\CookieHandler;
use cetver\LanguagesDispatcher\tests\AbstractUnitTest;
use Yii;
use yii\base\InvalidConfigException;
use yii\web\Application;
use yii\web\Request;
use yii\web\Response;

class CookieHandlerTest extends AbstractUnitTest
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
                new CookieHandler([
                    'request' => $request,
                ]);
            }
        );

        $response = 'invalid-response';
        $this->tester->expectException(
            new InvalidConfigException(sprintf(
                'The component with the specified ID "%s" must be an instance of "%s"',
                $response,
                Response::className()
            )),
            function () use ($response) {
                $this->mockWebApplication();
                new CookieHandler([
                    'response' => $response,
                ]);
            }
        );

        $cookieConfig = [
            'class' => 'stdClass',
        ];
        $this->tester->expectException(
            InvalidConfigException::class,
            function () use ($cookieConfig) {
                $this->mockWebApplication();
                new CookieHandler([
                    'cookieConfig' => $cookieConfig,
                ]);
            }
        );

        $expire = strtotime('+1 day');
        $handler = new CookieHandler([
            'cookieConfig' => [
                'expire' => $expire,
            ],
        ]);
        $this->tester->assertInstanceOf(Request::className(), $handler->request);
        $this->tester->assertInstanceOf(Response::className(), $handler->response);
        $this->tester->assertSame('yii\web\Cookie', $handler->cookieConfig['class']);
        $this->tester->assertSame('language', $handler->cookieConfig['name']);
        $this->tester->assertSame('', $handler->cookieConfig['domain']);
        $this->tester->assertSame($expire, $handler->cookieConfig['expire']);
        $this->tester->assertSame('/', $handler->cookieConfig['path']);
        $this->tester->assertSame(false, $handler->cookieConfig['secure']);
        $this->tester->assertSame(true, $handler->cookieConfig['httpOnly']);
    }

    public function testGetLanguages()
    {
        $config = [
            'components' => [
                'request' => [
                    'enableCookieValidation' => false,
                ],
            ],
        ];
        $this->mockWebApplication($config);
        $handler = new CookieHandler();
        $this->tester->assertSame([null], $handler->getLanguages());

        $this->mockWebApplication($config);
        $_COOKIE['language'] = 'ru';
        $handler = new CookieHandler();
        $this->tester->assertSame(['ru'], $handler->getLanguages());

        $this->mockWebApplication($config);
        $_COOKIE['lang'] = 'en';
        $handler = new CookieHandler([
            'cookieConfig' => [
                'name' => 'lang',
            ],
        ]);
        $this->tester->assertSame(['en'], $handler->getLanguages());
    }

    public function testSetCookie()
    {
        $this->mockWebApplication([
            'components' => [
                'request' => [
                    'enableCookieValidation' => false,
                ],
                'ld' => [
                    'handlers' => [
                        CookieHandler::className(),
                    ],
                ],
            ],
        ]);
        Yii::$app->trigger(Application::EVENT_BEFORE_ACTION);
        $this->tester->assertSame(Yii::$app->language, Yii::$app->getResponse()->getCookies()->get('language')->value);
    }
}