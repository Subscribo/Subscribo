<?php namespace Subscribo\RestCommon;

class SignatureOptions
{
    /** @var null|string */
    public $signatureType;

    /** @var null|string */
    public $signatureVersion;

    /** @var null|array */
    public $dataKeys;

    /** @var null|string */
    public $nonce;

    /** @var  null|string */
    public $timestamp;

    /** @var array */
    protected $descriptionAdds = array();

    /** @var  string */
    public $locale;




    public function __construct(array $data = array())
    {
        if ( ! empty($data)) {
            $this->import($data);
        }
    }


    public function import(array $data)
    {
        if ( ! empty($data['descriptionAdds'])) {
            $this->descriptionAdds = $data['descriptionAdds'];
        }
        if ( ! empty($data['signatureType'])) {
            $this->signatureType = $data['signatureType'];
        }
        if ( ! empty($data['signatureVersion'])) {
            $this->signatureVersion = $data['signatureVersion'];
        }
        if ( ! empty($data['dataKeys'])) {
            $this->dataKeys = $data['dataKeys'];
        }
        if ( ! empty($data['nonce'])) {
            $this->nonce = $data['nonce'];
        }
        if ( ! empty($data['timestamp'])) {
            $this->timestamp = $data['timestamp'];
        }
        if ( ! empty($data['locale'])) {
            $this->locale = $data['locale'];
        }
        if ( ! empty($data['accountId'])) {
            $this->descriptionAdds['accountId'] = $data['accountId'];
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getDescriptionAdds()
    {
        return $this->descriptionAdds;
    }
}
