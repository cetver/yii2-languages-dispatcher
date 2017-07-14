<?php

namespace cetver\LanguagesDispatcher\handlers;

use Yii;
use yii\base\InvalidConfigException;
use yii\web\Request;

/**
 * Class QueryParamHandler handles the languages by the query parameter.
 *
 * @package cetver\LanguagesDispatcher\handlers
 */
class QueryParamHandler extends AbstractHandler
{
    /**
     * @var string|Request the Request component ID.
     */
    public $request = 'request';
    /**
     * @var string the query parameter name that contains a language.
     * @see Request::getQueryParams()
     */
    public $queryParam = 'language';

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
        return [
            $this->request->getQueryParam($this->queryParam),
        ];
    }
}