<?php

namespace cetver\LanguagesDispatcher\handlers;

use yii\base\BaseObject;

/**
 * Class AbstractHandler is a simple handler implementation that other handlers can inherit from.
 *
 * @package cetver\LanguagesDispatcher\handlers
 */
abstract class AbstractHandler extends BaseObject
{
    /**
     * Returns the list of languages detected by the handler.
     *
     * @return array list of languages.
     */
    public function getLanguages()
    {
        return [];
    }
}
