<?php

namespace cetver\LanguagesDispatcher\tests\handlers;

use cetver\LanguagesDispatcher\handlers\UserHandler;
use cetver\LanguagesDispatcher\tests\_data\models\UserActiveRecord;
use cetver\LanguagesDispatcher\tests\_data\models\UserObject;
use cetver\LanguagesDispatcher\tests\AbstractUnitTest;
use Exception;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\web\Application;
use yii\web\User;

class UserHandlerTest extends AbstractUnitTest
{
    public function testInit()
    {
        $user = 'invalid-user';
        $this->tester->expectException(
            new InvalidConfigException(sprintf(
                'The component with the specified ID "%s" must be an instance of "%s"',
                $user,
                User::className()
            )),
            function () use ($user) {
                $this->mockWebApplication();
                new UserHandler([
                    'user' => $user,
                ]);
            }
        );

        $this->tester->expectException(
            new InvalidConfigException(sprintf(
                'The "%s::getIdentity()" method must return an instance of "%s"',
                User::className(),
                ActiveRecord::className()
            )),
            function () {
                $this->mockWebApplication([
                    'components' => [
                        'user' => [
                            'identityClass' => 'cetver\LanguagesDispatcher\tests\_data\models\UserObject',
                        ],
                    ],
                ]);
                Yii::$app->getUser()->login(UserObject::findByUsername('admin'));
                new UserHandler();
            }
        );

        $languageAttribute = 'invalid-language-attribute';
        $this->tester->expectException(
            new InvalidConfigException(sprintf(
                'The "%s" property does not exists in the "%s" class',
                $languageAttribute,
                UserActiveRecord::className()
            )),
            function () use ($languageAttribute) {
                $this->mockWebApplication([
                    'components' => [
                        'user' => [
                            'identityClass' => UserActiveRecord::className(),
                        ],
                    ],
                ]);
                $this->initUsersTable(Yii::$app);
                Yii::$app->getUser()->login(UserActiveRecord::findByUsername('admin'));
                new UserHandler([
                    'languageAttribute' => $languageAttribute,
                ]);
            }
        );
    }

    public function testGetLanguages()
    {
        $config = [
            'components' => [
                'user' => [
                    'identityClass' => UserActiveRecord::className(),
                ],
            ],
        ];
        $this->mockWebApplication($config);
        $handler = new UserHandler();
        $this->tester->assertSame([], $handler->getLanguages());

        $this->mockWebApplication($config);
        $this->initUsersTable(Yii::$app);
        Yii::$app->getUser()->login(UserActiveRecord::findByUsername('admin'));
        $handler = new UserHandler();
        $this->tester->assertSame([null], $handler->getLanguages());

        $this->mockWebApplication($config);
        $this->initUsersTable(Yii::$app);
        Yii::$app->getDb()->createCommand()->update('users', ['language_code' => 'ru'])->execute();
        Yii::$app->getUser()->login(UserActiveRecord::findByUsername('admin'));
        $handler = new UserHandler();
        $this->tester->assertSame(['ru'], $handler->getLanguages());
    }

    public function testSaveAttribute()
    {
        /**
         * @var $identity UserActiveRecord
         */
        $this->mockWebApplication([
            'components' => [
                'user' => [
                    'identityClass' => UserActiveRecord::className(),
                ],
            ],
        ]);
        $this->initUsersTable(Yii::$app);
        Yii::$app->getUser()->login(UserActiveRecord::findByUsername('admin'));
        new UserHandler();
        Yii::$app->trigger(Application::EVENT_BEFORE_ACTION);
        $identity = Yii::$app->getUser()->getIdentity();
        $this->tester->assertSame(Yii::$app->language, $identity->language_code);
    }

    protected function initUsersTable(Application $app)
    {
        $app->set('db', [
            'class' => 'yii\db\Connection',
            'dsn' => 'sqlite::memory:',
        ]);
        $db = $app->getDb();
        $schema = $db->getSchema();
        $transaction = $db->beginTransaction();
        try {
            $db->createCommand('DROP TABLE IF EXISTS {{users}}')->execute();
            $db
                ->createCommand()
                ->createTable('users', [
                    'id' => $schema->createColumnSchemaBuilder($schema::TYPE_PK),
                    'language_code' => $schema->createColumnSchemaBuilder($schema::TYPE_STRING, 255),
                    'username' => $schema->createColumnSchemaBuilder($schema::TYPE_STRING, 255),
                    'auth_key' => $schema->createColumnSchemaBuilder($schema::TYPE_STRING, 255),
                ])
                ->execute();
            $db
                ->createCommand()
                ->insert('users', [
                    'language_code' => null,
                    'username' => 'admin',
                    'auth_key' => 'test100key',
                ])
                ->execute();
            $transaction->commit();
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
}