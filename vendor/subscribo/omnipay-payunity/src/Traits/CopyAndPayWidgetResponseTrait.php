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
     * @param string $returnUrl absolute url for processing the result
     * @param array|string $brands
     * @return string
     */
    public static function assembleWidgetForm($token, $returnUrl, $brands)
    {
        if (is_array($brands)) {
            $brands = implode(' ', $brands);
        }
        $result = '<form action="'.$returnUrl.'" id="'.$token.'">'.$brands.'</form>';
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
     * @param array|string|null $brands
     * @param string|null $returnUrl absolute url for processing the result
     * @return null|string
     */
    public function getWidget($language = null, $style = null, $compressed = true, $brands = null, $returnUrl = null)
    {
        if ( ! $this->isTransactionToken()) {
            return null;
        }
        $result = $this->getWidgetJavascript($language, $style, $compressed).$this->getWidgetForm($brands, $returnUrl);
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
     * @param null|string|array brands
     * @param null|string $returnUrl absolute url for processing the result
     * @return null|string
     * @throws InvalidArgumentException
     */
    public function getWidgetForm($brands = null, $returnUrl = null)
    {
        $token = $this->getTransactionToken();
        if (empty($token)) {
            return null;
        }
        // If $brands was not provided as a parameter, we try to get it from request
        if (is_null($brands)) {
            /** @var \Omnipay\PayUnity\Message\AbstractRequest|null $request */
            $request = $this->getRequest();
            $parameters = $request->getParameters();
            $brands = isset($parameters['brands']) ? $parameters['brands'] : null;
        }
        if (is_null($brands)) {
            throw new InvalidArgumentException('Parameter brands (string or array) was not provided neither found in request');
        }
        // If $returnUrl was not provided as a parameter, we try to get it from request
        if (empty($returnUrl)) {
            /** @var \Omnipay\Common\Message\AbstractRequest|null $request */
            $request = $this->getRequest();
            $returnUrl = $request ? $request->getReturnUrl() : null;
        }
        if (empty($returnUrl)) {
            throw new InvalidArgumentException('Parameter returnUrl was not provided neither found in request');
        }
        $result = static::assembleWidgetForm($token, $returnUrl, $brands);
        return $result;
    }
}
