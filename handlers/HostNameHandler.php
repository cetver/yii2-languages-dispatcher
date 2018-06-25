<?php

namespace cetver\LanguagesDispatcher\handlers;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;
use yii\web\Request;

/**
 * Class HostNameHandler
 *
 * @package cetver\LanguagesDispatcher\handlers
 *
 * @property array          $hostMap
 * @property array          $_languages
 * @property Request|string $request
 */
class HostNameHandler extends AbstractHandler
{
    /**
     * @var string|Request the Request component ID.
     */
    public $request = 'request';

    /** @var array
     *
     * key represents the host name and the value represents the language code
     */
    public $hostMap = [];

    /**
     * @var array
     */
    private $_languages = [];

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

        if (!is_array($this->hostMap)) throw new InvalidConfigException("hostMap must be an array");
    }

    /**
     * @inheritdoc
     */
    public function getLanguages()
    {
        if (empty($this->_languages)) {
            try {
                // other handlers will populate this
                $this->_languages[] = $this->hostMap[$this->request->hostName];
            } catch (\Exception $exception) {
                Yii::error($exception->getMessage());
            }
        }

        return ArrayHelper::merge(parent::getLanguages(), $this->_languages);
    }
}