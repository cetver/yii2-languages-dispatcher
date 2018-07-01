<?php

namespace cetver\LanguagesDispatcher\handlers;

use Yii;
use yii\base\InvalidConfigException;
use yii\web\Request;

/**
 * Class HostNameHandler handles languages based on the hostname of the request.
 */
class HostNameHandler extends AbstractHandler
{
    /**
     * @var string|Request the Request component ID.
     */
    public $request = 'request';

    /** @var array|callable An array that maps hostnames to languages or a callable function that returns it.
     */
    public $hostMap;

    /**
     * @inheritdoc
     */
    function init()
    {
        parent::init();

        $request = Yii::$app->get($this->request, false);
        if (!$request instanceof Request) {
            throw new InvalidConfigException(sprintf(
                'The component with the specified ID "%s" must be an instance of "%s"',
                $this->request,
                Request::className()
            ));
        }

        $this->request = $request;

        if (is_callable($this->hostMap)) {
            $this->hostMap = call_user_func($this->hostMap);
        }

        if (!is_array($this->hostMap)) {
            throw new InvalidConfigException(
                'The "hostMap" property must be an array or callable function that returns an array'
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function getLanguages()
    {
        return (isset($this->hostMap[$this->request->hostName]))
            ? [$this->hostMap[$this->request->hostName]]
            : [];
    }
}