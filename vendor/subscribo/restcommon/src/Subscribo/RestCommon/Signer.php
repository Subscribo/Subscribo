<?php namespace Subscribo\RestCommon;

use Subscribo\RestCommon\TokenRing;
use Subscribo\RestCommon\Signature;
use Illuminate\Encryption\Encrypter;
use Symfony\Component\HttpFoundation\Request;

class Signer
{
    protected $tokenRing;
    protected $encrypter;


    public function __construct($tokenRing)
    {
        if ($tokenRing instanceof TokenRing) {
            $this->tokenRing = $tokenRing;
        } else {
            $this->tokenRing =  new TokenRing($tokenRing);
        }
        if ($this->tokenRing->commonSecret) {
            $this->encrypter = new Encrypter($this->tokenRing->commonSecret);
        }
    }

    public function signRequest(Request $request, $options = array())
    {
        $result = Signature::signRequest($request, $this->tokenRing, $options, $this->encrypter);
        return $result;
    }

    public function modifyHeaders(array $headers = array(), array $data = array(), array $options = array())
    {
        $result = Signature::modifyHeaders($this->tokenRing, $headers, $data, $options, $this->encrypter);
        return $result;
    }
}