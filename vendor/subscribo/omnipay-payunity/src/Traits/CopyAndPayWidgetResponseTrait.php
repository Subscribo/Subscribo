<?php

namespace Omnipay\PayUnity\Traits;

use InvalidArgumentException;

trait CopyAndPayWidgetResponseTrait
{
    /**
     * @param string $token
     * @param bool $testMode
     * @param bool $compressed
     * @param string|null $language For example: en
     * @param string|null $style Possibilities: card plain none
     * @return string
     */
    public static function assembleWidgetJavascriptUrl($token, $testMode = true, $language = null, $style = null, $compressed = true)
    {
        $urlBase = $testMode
            ? 'https://test.ctpe.net/frontend/widget/v4/widget.js;jsessionid='
            : 'https://ctpe.net/frontend/widget/v4/widget.js;jsessionid=';
        $url = $urlBase.$token;
        $queryParameters = [];
        if ($testMode and ! $compressed) {
            $queryParameters['compressed'] = 'false';
        }
        if ($language) {
            $queryParameters['language'] = $language;
        }
        if ($style) {
            $queryParameters['style'] = $style;
        }
        if ($queryParameters) {
            $url .= '?'.http_build_query($queryParameters);
        }
        return $url;
    }

    /**
     * @param string $token
     * @param bool $testMode
     * @param bool $compressed
     * @param string|null $language For example: en
     * @param string|null $style Possibilities: card plain none
     * @return string
     */
    public static function assembleWidgetJavascript($token, $testMode = true, $language = null, $style = null, $compressed = true)
    {
        $url = static::assembleWidgetJavascriptUrl($token, $testMode, $language, $style, $compressed);
        $result = '<script async src="'.$url.'" ></script>';
        return $result;
    }

    /**
     * @param string $token
     * @param string $redirectUrl
     * @return string
     */
    public static function assembleWidgetForm($token, $redirectUrl)
    {
        $result = '<form action="'.$redirectUrl.'" id="'.$token.'"></form>';
        return $result;
    }

    public function isSuccessful()
    {
        return false;
    }

    public function isTransactionToken()
    {
        return ( ! empty($this->data['transaction']['token']));
    }

    public function haveWidget()
    {
        return $this->isTransactionToken();
    }

    public function getTransactionToken()
    {
        return empty($this->data['transaction']['token']) ? null : $this->data['transaction']['token'];
    }

    /**
     * @param string|null $language
     * @param string|null $style
     * @param bool $compressed default true for compressed javascript
     * @param string|null $redirectUrl
     * @return null|string
     */
    public function getWidget($language = null, $style = null, $compressed = true, $redirectUrl = null)
    {
        if ( ! $this->isTransactionToken()) {
            return null;
        }
        $result = $this->getWidgetJavascript($language, $style, $compressed).$this->getWidgetForm($redirectUrl);
        return $result;
    }

    /**
     * @param string|null $language
     * @param string|null $style
     * @param bool $compressed
     * @return null|string
     */
    public function getWidgetJavascript($language = null, $style = null, $compressed = true)
    {
        $token = $this->getTransactionToken();
        if (empty($token)) {
            return null;
        }
        /** @var \Omnipay\Common\Message\AbstractRequest|null $request */
        $request = $this->getRequest();
        $testMode = $request ? $request->getTestMode() : true;
        $result = static::assembleWidgetJavascript($token, $testMode, $language, $style, $compressed);
        return $result;
    }

    /**
     * @param null|string $redirectUrl
     * @return null|string
     * @throws InvalidArgumentException
     */
    public function getWidgetForm($redirectUrl = null)
    {
        $token = $this->getTransactionToken();
        if (empty($token)) {
            return null;
        }
        // If $redirectUrl was not provided as a parameter, we try to get it from request
        if (empty($redirectUrl)) {
            /** @var \Omnipay\Common\Message\AbstractRequest|null $request */
            $request = $this->getRequest();
            $redirectUrl = $request ? $request->getReturnUrl() : null;
        }
        if (empty($redirectUrl)) {
            throw new InvalidArgumentException('Parameter redirectUrl was not provided neither found in request');
        }
        $result = static::assembleWidgetForm($token, $redirectUrl);
        return $result;
    }
}
