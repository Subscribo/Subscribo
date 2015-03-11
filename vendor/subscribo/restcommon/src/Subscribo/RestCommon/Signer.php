<?php namespace Subscribo\RestCommon;

use Subscribo\RestCommon\TokenRing;
use Subscribo\RestCommon\Signature;
use Illuminate\Encryption\Encrypter;
use Symfony\Component\HttpFoundation\Request;
use Subscribo\RestCommon\Exceptions\InvalidArgumentException;

class Signer
{
    protected $tokenRing;
    protected $encrypter;


    public function __construct($tokenRing)
    {
        if (empty($tokenRing)) {
            throw new InvalidArgumentException('Signer::__construct() Parameter tokenRing should not be empty');
        }
        $this->tokenRing =  TokenRing::make($tokenRing);
        if ($this->tokenRing->commonSecret) {
            $this->encrypter = new Encrypter($this->tokenRing->commonSecret);
        }
    }

    public function signRequest(Request $request, $options = array())
    {
        $result = Signature::signRequest($request, $this->tokenRing, $options, $this->encrypter);
        return $result;
    }

    public function modifyHeaders(array $headers = array(), array $data = array(), $options = null)
    {
        $options = empty($options) ? array() : $options;
        $result = Signature::modifyHeaders($this->tokenRing, $headers, $data, $options, $this->encrypter);
        return $result;
    }
}