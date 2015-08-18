<?php namespace Subscribo\RestCommon;

use Subscribo\RestCommon\ServerRequest;

class ClientRedirection extends ServerRequest
{
    const TYPE = 'clientRedirection';

    const CODE_CONFIRM_MERGE_REQUEST = 100;

    const CODE_CONFIRM_MERGE_RESPONSE = 200;

    const CODE_GENERIC_REDIRECTION = 1000;

    /** @var string|null */
    public $urlPattern;

    /** @var string|null */
    public $urlSimple;

    /** @var bool   */
    public $remember;


    public function import(array $data)
    {
        if (isset($data['urlPattern'])) {
            $this->urlPattern = $data['urlPattern'];
        }
        if (isset($data['urlSimple'])) {
            $this->urlSimple = $data['urlSimple'];
        }
        if (isset($data['remember'])) {
            $this->remember = $data['remember'];
        }
        return parent::import($data);
    }

    public function export()
    {
        $result = parent::export();
        $result['urlPattern'] = $this->urlPattern;
        $result['urlSimple'] = $this->urlSimple;
        $result['remember'] = $this->remember;
        return $result;
    }

    /**
     * @param null $redirectBackUrl
     * @return string|null
     */
    public function getUrl($redirectBackUrl = null)
    {
        if ($this->urlPattern) {
            $replacements = [
                '{hash}' => $this->hash,
                '{redirect_back}' => urlencode($redirectBackUrl),
            ];
            $result = strtr($this->urlPattern, $replacements);
            return $result;
        }
        return $this->urlSimple;
    }

}
