<?php

namespace cetver\LanguagesDispatcher\tests;

use Codeception\Test\Unit;
use yii\helpers\ArrayHelper;
use yii\web\Application;

abstract class AbstractUnitTest extends Unit
{
    /**
     * @var \cetver\LanguagesDispatcher\tests\UnitTester
     */
    protected $tester;

    protected $languages = [
        'en',
        'ru',
        'de'
    ];

    protected function mockWebApplication($config = [])
    {
        new Application(ArrayHelper::merge(
            [
                'id' => 'test-app',
                'basePath' => __DIR__,
                'bootstrap' => ['ld'],
                'components' => [
                    'ld' => [
                        'class' => 'cetver\LanguagesDispatcher\Component',
                    ],
                    'request' => [
                        'cookieValidationKey' => 'cookieValidationKey'
                    ]
                ]
            ],
            $config
        ));
    }
}