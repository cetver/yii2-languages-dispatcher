Languages dispatcher
====================


[![Build Status](https://travis-ci.org/cetver/yii2-languages-dispatcher.svg?branch=master)](https://travis-ci.org/cetver/yii2-languages-dispatcher)
[![Coverage Status](https://coveralls.io/repos/github/cetver/yii2-languages-dispatcher/badge.svg?branch=master)](https://coveralls.io/github/cetver/yii2-languages-dispatcher?branch=master)

Sets the web-application language

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
composer require --prefer-dist cetver/yii2-languages-dispatcher
```

or add

```
"cetver/yii2-languages-dispatcher": "^1.0"
```

to the require section of your `composer.json` file.


Usage
-----

Update the web-application configuration file

```php
return [
    'bootstrap' => ['languagesDispatcher'],
    'components' => [
        'languagesDispatcher' => [
            'class' => 'cetver\LanguagesDispatcher\Component',
            'languages' => ['en', 'ru'],
            // useful if you want to push the language handler at the beginning of beforeAction event handlers list
            'appendSetLanguageHandler' => false, // defaults to true
            /*
            or
            'languages' => function () {
                return \app\models\Language::find()->select('code')->column();
            },
            */
            // Order is important
            'handlers' => [
                [
                    // Detects a language based on host name
                    'class' => 'cetver\LanguagesDispatcher\handlers\HostNameHandler',
                    'request' => 'request', // optional, the Request component ID.                    
                    'hostMap' => [ // An array that maps hostnames to languages or a callable function that returns it.
                        'en.example.com' => 'en',
                        'ru.example.com' => 'ru'
                    ]
                ],
                [
                    // Detects a language from the query parameter.
                    'class' => 'cetver\LanguagesDispatcher\handlers\QueryParamHandler',
                    'request' => 'request', // optional, the Request component ID.
                    'queryParam' => 'language' // optional, the query parameter name that contains a language.
                ],
                [
                    // Detects a language from the session.
                    // Writes a language to the session, regardless of what handler detected it.
                    'class' => 'cetver\LanguagesDispatcher\handlers\SessionHandler',
                    'session' => 'session', // optional, the Session component ID.
                    'key' => 'language' // optional, the session key that contains a language.
                ],
                [
                    // Detects a language from the cookie.
                    // Writes a language to the cookie, regardless of what handler detected it.
                    'class' => 'cetver\LanguagesDispatcher\handlers\CookieHandler',
                    'request' => 'request', // optional, the Request component ID.
                    'response' => 'response', // optional, the Response component ID.
                    'cookieConfig' => [ // optional, the Cookie component configuration.
                        'class' => 'yii\web\Cookie',
                        'name' => 'language',
                        'domain' => '',
                        'expire' => strtotime('+1 year'),
                        'path' => '/',
                        'secure' => true | false, // depends on Request::$isSecureConnection
                        'httpOnly' => true,
                    ]
                ],
                [
                    // Detects a language from an authenticated user.
                    // Writes a language to an authenticated user, regardless of what handler detected it.
                    // Note: The property "identityClass" of the "User" component must be an instance of "\yii\db\ActiveRecord"
                    'class' => 'cetver\LanguagesDispatcher\handlers\UserHandler',
                    'user' => 'user',  // optional, the User component ID.
                    'languageAttribute' => 'language_code' // optional, an attribute that contains a language.
                ],
                [
                    // Detects a language from the "Accept-Language" header.
                    'class' => 'cetver\LanguagesDispatcher\handlers\AcceptLanguageHeaderHandler',
                    'request' => 'request', // optional, the Request component ID.
                ],
                [
                    // Detects a language from the "language" property.
                    'class' => 'cetver\LanguagesDispatcher\handlers\DefaultLanguageHandler',
                    'language' => 'en' // the default language.
                    /*
                    or
                    'language' => function () {
                        return \app\models\Language::find()
                            ->select('code')
                            ->where(['is_default' => true])
                            ->createCommand()
                            ->queryScalar();
                    },
                    */
                ]

            ],
        ],
    ],
];
```

Tests
-----

Run the following commands

```
composer create-project --prefer-source cetver/yii2-languages-dispatcher
cd yii2-languages-dispatcher
vendor/bin/codecept run unit
```

For I18N support, take a look at
-------------------------------------
- [https://github.com/cetver/yii2-languages-dispatcher](https://github.com/cetver/yii2-languages-dispatcher) - Sets the web-application language
- [https://github.com/cetver/yii2-language-url-manager](https://github.com/cetver/yii2-language-url-manager) - Parses and creates URLs containing languages
- [https://github.com/cetver/yii2-language-selector](https://github.com/cetver/yii2-language-selector) - Provides the configuration for the language selector
- [https://github.com/creocoder/yii2-translateable](https://github.com/creocoder/yii2-translateable) - The translatable behavior (Active Record)