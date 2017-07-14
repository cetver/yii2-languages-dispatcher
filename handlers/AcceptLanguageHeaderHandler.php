<?php

namespace cetver\LanguagesDispatcher\handlers;

use Yii;
use yii\base\InvalidConfigException;
use yii\web\Request;

/**
 * Class AcceptLanguageHeaderHandler handles the languages by Accept-Language header.
 *
 * @package cetver\LanguagesDispatcher\handlers
 */
class AcceptLanguageHeaderHandler extends AbstractHandler
{
    /**
     * @var string|Request the Request component ID.
     */
    public $request = 'request';

    /**
     * @inheritdoc
     */
    public function init()
    {
        $request = Yii::$app->get($this->request, false);
        if (!$request instanceof Request) {
            throw new InvalidConfigException(sprintf(
                'The component with the specified ID "%s" must be an instance of "%s"',
                $this->request,
                Request::className()
            ));
        }
        $this->request = $request;
    }

    /**
     * @inheritdoc
     */
    public function getLanguages()
    {
        return $this->request->getAcceptableLanguages();
    }
}