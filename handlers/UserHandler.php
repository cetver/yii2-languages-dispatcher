<?php

namespace cetver\LanguagesDispatcher\handlers;

use cetver\LanguagesDispatcher\Component;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\web\User;

/**
 * Class UserHandler handles the languages by an authenticated user.
 *
 * @package cetver\LanguagesDispatcher\handlers
 */
class UserHandler extends AbstractHandler
{
    /**
     * @var string the User component ID.
     */
    public $user = 'user';
    /**
     * @var string an attribute that contains a language.
     */
    public $languageAttribute = 'language_code';
    /**
     * @var null|\yii\web\IdentityInterface|ActiveRecord
     */
    protected $identity;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $user = Yii::$app->get($this->user, false);
        if (!$user instanceof User) {
            throw new InvalidConfigException(sprintf(
                'The component with the specified ID "%s" must be an instance of "%s"',
                $this->user,
                User::className()
            ));
        }
        $this->identity = $user->getIdentity();
        if ($this->identity !== null) {
            if (!$this->identity instanceof ActiveRecord) {
                throw new InvalidConfigException(sprintf(
                    'The "%s::getIdentity()" method must return an instance of "%s"',
                    $user::className(),
                    ActiveRecord::className()
                ));
            }
            if (!$this->identity->hasProperty($this->languageAttribute)) {
                throw new InvalidConfigException(sprintf(
                    'The "%s" property does not exists in the "%s" class',
                    $this->languageAttribute,
                    get_class($this->identity)
                ));
            }
            Yii::$app->on(Component::EVENT_AFTER_SETTING_LANGUAGE, [$this, 'saveAttribute']);
        }
    }

    /**
     * @inheritdoc
     */
    public function getLanguages()
    {
        return ($this->identity === null) ? [] : [$this->identity->{$this->languageAttribute}];
    }

    /**
     * Saves the language attribute.
     */
    public function saveAttribute()
    {
        $language = current($this->getLanguages());
        if ($language !== Yii::$app->language) {
            $this->identity->{$this->languageAttribute} = Yii::$app->language;
            $this->identity->save(true, [$this->languageAttribute]);
        }
    }
}