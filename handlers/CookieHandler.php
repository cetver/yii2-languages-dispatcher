<?php

namespace cetver\LanguagesDispatcher\handlers;

use cetver\LanguagesDispatcher\Component;
use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\VarDumper;
use yii\web\Cookie;
use yii\web\Request;
use yii\web\Response;

/**
 * Class CookieHandler handles the languages by the cookies.
 *
 * @package cetver\LanguagesDispatcher\handlers
 */
class CookieHandler extends AbstractHandler
{
    /**
     * @var string|Request the Request component ID.
     */
    public $request = 'request';
    /**
     * @var string|Response the Response component ID.
     */
    public $response = 'response';
    /**
     * @var array the Cookie component configuration.
     * @see Cookie
     */
    public $cookieConfig = [];
    /**
     * @var Cookie the Cookie component instance.
     */
    protected $cookie;

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
        $response = Yii::$app->get($this->response, false);
        if (!$response instanceof Response) {
            throw new InvalidConfigException(sprintf(
                'The component with the specified ID "%s" must be an instance of "%s"',
                $this->response,
                Response::className()
            ));
        }
        $this->response = $response;
        $this->cookieConfig = array_merge(
            [
                'class' => Cookie::className(),
                'name' => 'language',
                'domain' => '',
                'expire' => strtotime('+1 year'),
                'path' => '/',
                'secure' => $this->request->isSecureConnection,
                'httpOnly' => true,
            ],
            $this->cookieConfig
        );
        $this->cookie = Yii::createObject($this->cookieConfig);
        if (!$this->cookie instanceof Cookie) {
            throw new InvalidConfigException(sprintf(
                'Could not create an instance of "%s" by the following configuration: %s',
                Cookie::className(),
                VarDumper::dumpAsString($this->cookieConfig)
            ));
        }
        Yii::$app->on(Component::EVENT_AFTER_SETTING_LANGUAGE, [$this, 'setCookie']);
    }

    /**
     * @inheritdoc
     */
    public function getLanguages()
    {
        return [
            $this->request->getCookies()->getValue($this->cookie->name),
        ];
    }

    /**
     * Sets the cookie.
     */
    public function setCookie()
    {
        $language = current($this->getLanguages());
        if ($language !== Yii::$app->language) {
            $this->cookie->value = Yii::$app->language;
            $this->response->getCookies()->add($this->cookie);
        }
    }
}