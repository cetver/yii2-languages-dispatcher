<?php

namespace cetver\LanguagesDispatcher\handlers;

use cetver\LanguagesDispatcher\Component;
use Yii;
use yii\base\InvalidConfigException;
use yii\web\MultiFieldSession;
use yii\web\Session;

/**
 * Class SessionHandler handles the languages by the session.
 *
 * @package cetver\LanguagesDispatcher\handlers
 */
class SessionHandler extends AbstractHandler
{
    /**
     * @var string|Session the Session component ID.
     */
    public $session = 'session';
    /**
     * @var string the session key that contains a language.
     * @see Session::get()
     */
    public $key = 'language';

    /**
     * @inheritdoc
     */
    public function init()
    {
        $session = Yii::$app->get($this->session, false);
        if (!$session instanceof Session && !$session instanceof MultiFieldSession) {
            throw new InvalidConfigException(sprintf(
                'The component with the specified ID "%s" must be an instance of "%s" or "%s"',
                $this->session,
                Session::className(),
                MultiFieldSession::className()
            ));
        }
        $this->session = $session;
        Yii::$app->on(Component::EVENT_AFTER_SETTING_LANGUAGE, [$this, 'setSession']);
    }

    /**
     * @inheritdoc
     */
    public function getLanguages()
    {
        return [
            $this->session->get($this->key),
        ];
    }

    /**
     * Sets the session.
     */
    public function setSession()
    {
        $this->session->set($this->key, Yii::$app->language);
    }
}