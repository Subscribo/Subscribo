<?php namespace Subscribo\RestCommon;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\HeaderBag;
use Subscribo\RestCommon\RestCommon;
use Subscribo\Support\Arr;
use Subscribo\RestCommon\Exceptions\InvalidArgumentException;
use Illuminate\Contracts\Encryption\Encrypter;
use Subscribo\RestCommon\TokenRing;

class Signature {

    const TYPE_SUBSCRIBO_DIGEST = 'SubscriboDigest';
    const TYPE_SUBSCRIBO_BASIC = 'SubscriboBasic';

    const HASH_HMAC_ALGORITHM = 'sha512';



    public static function signRequest(Request $request, TokenRing $tokenRing, array $options = array(), Encrypter $encrypter = null, array &$description = array())
    {
        $result = clone $request;
        $headers = $request->headers->all();
        $data = [
            'requestUri'        => $request->getUri(),
            'requestMethod'     => $request->getMethod(),
            'requestContent'    => $request->getContent(),
        ];
        $headers = self::modifyHeaders($tokenRing, $headers, $data, $options, $encrypter, $description);

        $result->headers = new HeaderBag($headers);
        return $result;
    }

    public static function modifyHeaders(TokenRing $tokenRing, array $headers = array(), array $data = array(), array $options = array(), Encrypter $encrypter = null, array &$description = array())
    {
        $headerName = RestCommon::ACCESS_TOKEN_HEADER_FIELD_NAME;
        $headers = Arr::withoutKeyCaseInsensitively($headerName, $headers);
        $headers[$headerName] = self::assembleAuthorizationHeaderContent($tokenRing, $data, $options, $encrypter, $description);
        return $headers;
    }




    public static function assembleAuthorizationHeaderContent(TokenRing $tokenRing, array $data = array(), array $options = array(), Encrypter $encrypter = null, array &$description = array())
    {
        $signatureType = Arr::get($options, 'signatureType') ?: $tokenRing->ascertainType();
        if (empty($signatureType)) {
            throw new InvalidArgumentException('Signature type not provided');
        }
        if ( ! $tokenRing->check($signatureType)) {
            throw new InvalidArgumentException("We do not have enough information for Signature type or Signature type is not recognized (signatureType: '%s').", $signatureType);
        }
        switch ($signatureType) {
            case self::TYPE_SUBSCRIBO_BASIC:
                $result = self::TYPE_SUBSCRIBO_BASIC.' '.$tokenRing->basicToken;
                return $result;
            case self::TYPE_SUBSCRIBO_DIGEST:
                $description = self::assembleDigestDescription($tokenRing, $data, $options);
                $encoded = self::encode($description, $encrypter);
                $result = self::TYPE_SUBSCRIBO_DIGEST.' '.$encoded;
                return $result;
            default:
                throw new InvalidArgumentException(sprintf("Unrecognized signatureType '%s'", $signatureType));
        }
    }

    public static function assembleDigestDescription(TokenRing $tokenRing, array $data = array(), array $options = array())
    {
        $options['signatureType'] = Arr::get($options, 'signatureType', self::TYPE_SUBSCRIBO_DIGEST);
        $description = self::assembleDescriptionBase($tokenRing, $options);
        $description['dataKeys'] = Arr::get($options, 'dataKeys') ?: array_keys($data);
        $arrayToHash = ['description' => $description, 'data' => $data];
        $stringToHash = json_encode($arrayToHash);
        $signature = hash_hmac(self::HASH_HMAC_ALGORITHM, $stringToHash, $tokenRing->digestSecret);
        $description['signature'] = $signature;
        return $description;
    }

    protected static function assembleDescriptionBase(TokenRing $tokenRing, array $options)
    {
        $nonce = Arr::get($options, 'nonce') ?: self::generateSalt();
        $timestamp = Arr::get($options, 'timestamp') ?: self::generateTimestamp();
        $description = [
            'signatureType' => $options['signatureType'],
            'signatureVersion' => Arr::get($options, 'signatureVersion','1.0'),
            'subscriboDigestToken' => $tokenRing->digestToken,
            'salt' => self::generateSalt(),
            'nonce' => $nonce,
            'timestamp' => $timestamp,
        ];
        return $description;
    }

    protected static function encode($data, Encrypter $encrypter = null)
    {
        if ( ! is_string($data)) {
            $data = json_encode($data);
        }
        if ($encrypter) {
            $data = $encrypter->encrypt($data);
        }
        $base64Encoded = base64_encode($data);
        $result = urlencode($base64Encoded);
        return $result;
    }

    public static function generateSalt($length = 32)
    {
        $bytes = openssl_random_pseudo_bytes($length);
        $printable = base64_encode($bytes);
        $result = substr($printable, 0, $length);
        return $result;
    }

    public static function generateTimestamp()
    {
        $microTimeString = microtime();
        $microTimeParts = explode(' ', $microTimeString);
        $microseconds = empty($microTimeParts[1]) ? null : $microTimeParts[1];
        $current = date('Y-m-d H:i:s');
        $result = $microseconds ? ($current.'.'.$microseconds) : $current;
        return $result;
    }

}
