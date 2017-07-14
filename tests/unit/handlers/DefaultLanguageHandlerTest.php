<?php

namespace cetver\LanguagesDispatcher\tests\handlers;

use cetver\LanguagesDispatcher\handlers\DefaultLanguageHandler;
use cetver\LanguagesDispatcher\tests\AbstractUnitTest;
use yii\base\InvalidConfigException;

class DefaultLanguageHandlerTest extends AbstractUnitTest
{
    public function testInit()
    {
        $this->tester->expectException(
            new InvalidConfigException(
                'The "language" property must be a string or callable function that returns a string'
            ),
            function () {
                $this->mockWebApplication();
                new DefaultLanguageHandler([
                    'language' => null,
                ]);
            }
        );

        $this->mockWebApplication();
        $handler = new DefaultLanguageHandler([
            'language' => 'ru',
        ]);
        $this->tester->assertSame('ru', $handler->language);

        $this->mockWebApplication();
        $handler = new DefaultLanguageHandler([
            'language' => function () {
                return 'ru';
            },
        ]);
        $this->tester->assertSame('ru', $handler->language);
    }

    public function testGetLanguages()
    {
        $this->mockWebApplication();
        $handler = new DefaultLanguageHandler([
            'language' => 'ru',
        ]);
        $this->tester->assertSame(['ru'], $handler->getLanguages());
    }
}