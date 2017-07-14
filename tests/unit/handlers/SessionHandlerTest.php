<?php

namespace cetver\LanguagesDispatcher\tests\handlers;

use cetver\LanguagesDispatcher\handlers\SessionHandler;
use cetver\LanguagesDispatcher\tests\AbstractUnitTest;
use Yii;
use yii\base\InvalidConfigException;
use yii\web\Application;
use yii\web\MultiFieldSession;
use yii\web\Session;

class SessionHandlerTest extends AbstractUnitTest
{
    public function testInit()
    {
        $session = 'invalid-session';
        $this->tester->expectException(
            new InvalidConfigException(sprintf(
                'The component with the specified ID "%s" must be an instance of "%s" or "%s"',
                $session,
                Session::className(),
                MultiFieldSession::className()

            )),
            function () use ($session) {
                $this->mockWebApplication();
                new SessionHandler([
                    'session' => $session,
                ]);
            }
        );

        $this->mockWebApplication();
        $handler = new SessionHandler();
        $this->tester->assertInstanceOf(Session::className(), $handler->session);

        $this->mockWebApplication([
            'components' => [
                'db' => [
                    'class' => 'yii\db\Connection',
                    'dsn' => 'sqlite::memory:',
                ],
                'session' => [
                    'class' => 'yii\web\DbSession',
                ],
            ],
        ]);
        $handler = new SessionHandler();
        $this->tester->assertInstanceOf(MultiFieldSession::className(), $handler->session);
    }

    public function testGetLanguages()
    {
        $this->mockWebApplication();
        $handler = new SessionHandler();
        $this->tester->assertSame([null], $handler->getLanguages());

        $this->mockWebApplication();
        Yii::$app->getSession()->set('language', 'ru');
        $handler = new SessionHandler();
        $this->tester->assertSame(['ru'], $handler->getLanguages());

        $this->mockWebApplication();
        Yii::$app->getSession()->set('lang', 'en');
        $handler = new SessionHandler(['key' => 'lang']);
        $this->tester->assertSame(['en'], $handler->getLanguages());
    }

    public function testSetSession()
    {
        $this->mockWebApplication([
            'components' => [
                'ld' => [
                    'handlers' => [
                        SessionHandler::className(),
                    ],
                ],
            ],
        ]);
        Yii::$app->trigger(Application::EVENT_BEFORE_ACTION);
        $this->tester->assertSame(Yii::$app->language, Yii::$app->getSession()->get('language'));
    }
}