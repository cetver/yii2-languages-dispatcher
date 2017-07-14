<?php

namespace cetver\LanguagesDispatcher\handlers;

use yii\base\InvalidConfigException;

/**
 * Class DefaultLanguageHandler handles the default language.
 *
 * @package cetver\LanguagesDispatcher\handlers
 */
class DefaultLanguageHandler extends AbstractHandler
{
    /**
     * @var string|callable the default language.
     */
    public $language;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if (is_callable($this->language)) {
            $this->language = call_user_func($this->language);
        }
        if (!is_string($this->language)) {
            throw new InvalidConfigException(
                'The "language" property must be a string or callable function that returns a string'
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function getLanguages()
    {
        return [
            $this->language,
        ];
    }
}