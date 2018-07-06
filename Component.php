<?php

namespace cetver\LanguagesDispatcher;

use cetver\LanguagesDispatcher\handlers\AbstractHandler;
use Yii;
use yii\base\BootstrapInterface;
use yii\base\Event;
use yii\base\InvalidConfigException;
use yii\web\Application;

/**
 * Class Component manages a set of language handlers.
 *
 * @package cetver\LanguagesDispatcher
 */
class Component extends \yii\base\Component implements BootstrapInterface
{
    /**
     * @event \yii\base\Event an event raised after setting the language.
     */
    const EVENT_AFTER_SETTING_LANGUAGE = 'afterSettingLanguage';
    /**
     * @var array|callable The list of available languages.
     */
    public $languages = [];
    /**
     * @var array|AbstractHandler[] the language handlers. Each array element represents a single AbstractHandler
     * instance or the configuration for creating the handler instance.
     */
    public $handlers = [];

    /**
     * Bootstrap method to be called during application bootstrap stage.
     *
     * @param \yii\base\Application $app the application currently running.
     *
     * @throws InvalidConfigException
     */
    public function bootstrap($app)
    {
        if ($app instanceof Application) {
            if (is_callable($this->languages)) {
                $this->languages = call_user_func($this->languages);
            }
            if (!is_array($this->languages)) {
                throw new InvalidConfigException(
                    'The "languages" property must be an array or callable function that returns an array'
                );
            }
            foreach ($this->handlers as &$handler) {
                if (is_object($handler) === false) {
                    $handler = Yii::createObject($handler);
                }
                if (!$handler instanceof AbstractHandler) {
                    throw new InvalidConfigException(sprintf(
                        'The handler must be an instance of "%s"',
                        AbstractHandler::className()
                    ));
                }
            }
            $app->on($app::EVENT_BEFORE_ACTION, [$this, 'setLanguage'], $app, false);
        }
    }

    /**
     * Sets the application language.
     *
     * @see Application::$language
     *
     * @param Event $event the event parameter used for an action event.
     */
    public function setLanguage(Event $event)
    {
        /**
         * @var $app Application
         */
        $app = $event->data;
        foreach ($this->handlers as $handler) {
            $intersection = array_intersect($this->languages, $handler->getLanguages());
            if (!empty($intersection)) {
                Yii::$app->language = current($intersection);
                break;
            }
        }
        $app->trigger(self::EVENT_AFTER_SETTING_LANGUAGE);
    }
}