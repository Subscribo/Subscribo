<?php namespace Subscribo\RestCommon;

use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\HeaderBag;
use Subscribo\RestCommon\RestCommon;
use Subscribo\Support\Arr;
use Subscribo\RestCommon\Exceptions\InvalidArgumentException;
use Illuminate\Contracts\Encryption\Encrypter;
use Subscribo\RestCommon\TokenRing;
use Subscribo\RestCommon\Interfaces\TokenRingProviderInterface;
use Subscribo\RestCommon\SignatureOptions;
use Subscribo\Support\Str;

class Signature {

    const TYPE_SUBSCRIBO_DIGEST = 'SubscriboDigest';
    const TYPE_SUBSCRIBO_BASIC = 'SubscriboBasic';

    const HASH_HMAC_ALGORITHM = 'sha512';


    /**
     * @param Request $request
     * @param TokenRing $tokenRing
     * @param SignatureOptions|array $options
     * @param Encrypter $encrypter
     * @param array $description
     * @return Request
     */
    public static function signRequest(Request $request, TokenRing $tokenRing, $options = array(), Encrypter $encrypter = null, array &$description = array())
    {
        $result = clone $request;
        $headers = $request->headers->all();
        $data = [
            'requestUri'        => $request->getUri(),
            'requestMethod'     => $request->getMethod(),
            'requestContent'    => $request->getContent(),
        ];
        $headers = static::modifyHeaders($tokenRing, $headers, $data, $options, $encrypter, $description);

        $result->headers = new HeaderBag($headers);
        return $result;
    }

    /**
     * @param TokenRing $tokenRing
     * @param array $headers
     * @param array $data
     * @param SignatureOptions|array $options
     * @param Encrypter $encrypter
     * @param array $description
     * @return array
     */
    public static function modifyHeaders(TokenRing $tokenRing, array $headers = array(), array $data = array(), $options = array(), Encrypter $encrypter = null, array &$description = array())
    {
        $headerName = RestCommon::ACCESS_TOKEN_HEADER_FIELD_NAME;
        $headers = Arr::withoutKeyCaseInsensitively($headerName, $headers);
        $headers[$headerName] = static::assembleAuthorizationHeaderContent($tokenRing, $data, $options, $encrypter, $description);
        return $headers;
    }

    /**
     * @param Request $request
     * @param callable $tokenToTokenRingProvider
     * @param Encrypter $encrypter
     * @param bool $enforcedSignatureType
     * @param array $data
     * @param array $options
     * @param bool $throwExceptions
     * @return array|null
     */
    public static function processIncomingRequest(Request $request, callable $tokenToTokenRingProvider,  Encrypter $encrypter = null, $enforcedSignatureType = false, array $data = array(), array $options = array(), $throwExceptions = false)
    {
        $result = static::verifyRequest($request, $tokenToTokenRingProvider,  $encrypter, $enforcedSignatureType, $data, $options, $throwExceptions);
        if (empty($result)) {
            return null;
        }
        //$result['originalRequest'] = $request;
        $result['processedRequest'] = $request;
        return $result;
    }

    /**
     * Extract 'description' from result of calling Signature::processIncomingRequest()
     *
     * @param array|null $processIncomingRequestResult
     * @return array
     */
    public static function extractDescriptionFromProcessIncomingRequestResult($processIncomingRequestResult)
    {
        if ( ! is_array($processIncomingRequestResult)) {
            return array();
        }
        if (empty($processIncomingRequestResult['description'])) {
            return array();
        }
        return $processIncomingRequestResult['description'];
    }

    /**
     * @param Request $request
     * @param callable $tokenToTokenRingProvider
     * @param Encrypter|null $encrypter
     * @param string|bool $enforcedSignatureType
     * @param array $data
     * @param array $options
     * @param bool $throwExceptions
     * @return array|null
     */
    public static function verifyRequest(Request $request, callable $tokenToTokenRingProvider,  Encrypter $encrypter = null, $enforcedSignatureType = false, array $data = array(), array $options = array(), $throwExceptions = false)
    {
        $headers = $request->headers->all();
        $description = static::extractDescriptionFromHeaders($headers, $encrypter, $throwExceptions);
        if (empty($description)) {
            return static::throwExceptionOrReturnNull($throwExceptions, "Signature::verifyRequest() description is missing or invalid");
        }
        $signatureType = static::extractSignatureTypeFromDescription($description);
        if (empty($signatureType)) {
            return static::throwExceptionOrReturnNull($throwExceptions, "Signature::verifyRequest() signatureType is missing from description");
        }
        if (($enforcedSignatureType) and ($enforcedSignatureType !== $signatureType)) {
            return static::throwExceptionOrReturnNull($throwExceptions, "Signature::verifyRequest() signatureType in description ('%s') is different then enforcedSignatureType ('%s')", $signatureType, $enforcedSignatureType);
        }
        $token = static::extractTokenFromDescription($description);
        if (empty($token)) {
            return static::throwExceptionOrReturnNull($throwExceptions, "Signature::verifyRequest() token missing");
        }
        $tokenRingProvider = $tokenToTokenRingProvider($token, $signatureType);
        if (empty($tokenRingProvider)) {
            return static::throwExceptionOrReturnNull($throwExceptions, "Signature::verifyRequest() TokenRingProvider has not been returned");
        }
        if ( ! ($tokenRingProvider instanceof TokenRingProviderInterface)) {
            return static::throwExceptionOrReturnNull($throwExceptions, "Signature::verifyRequest() returned TokenRingProvider is not an instance of TokenRingProviderInterface");
        }
        $tokenRingData = $tokenRingProvider->provideTokenRing();
        if (empty($tokenRingData)) {
            return static::throwExceptionOrReturnNull($throwExceptions, "Signature::verifyRequest() TokenRingData has not been provided");
        }
        $tokenRing = TokenRing::make($tokenRingData);
        if (empty($tokenRingData)) {
            return static::throwExceptionOrReturnNull($throwExceptions, "Signature::verifyRequest() TokenRing has not been made");
        }
        $data['requestUri'] = Arr::get($data, 'requestUri') ?: $request->getUri();
        $data['requestMethod'] = Arr::get($data, 'requestMethod') ?: $request->getMethod();
        $data['requestContent']= Arr::get($data, 'requestContent') ?: $request->getContent();
        $verified = static::verifyDescription($description, $tokenRing, $data, $options, $throwExceptions);
        if ( ! $verified) {
            return static::throwExceptionOrReturnNull($throwExceptions, "Signature::verifyRequest() Description not verified");
        }
        $result = [
            'verified' => $verified,
            'description' => $description,
            'token' => $token,
            'tokenType' => $signatureType,
            'tokenRing' => $tokenRing,
            'tokenRingProvider' => $tokenRingProvider,
        ];
        return $result;
    }

    public static function extractDescriptionFromHeaders(array $headers, Encrypter $encrypter = null, $throwExceptions = false)
    {
        $headerName = RestCommon::ACCESS_TOKEN_HEADER_FIELD_NAME;
        $headerContent = Arr::getSimpleCaseInsensitively($headers, $headerName);
        if (is_array($headerContent)) {
            $headerContent = reset($headerContent);
        }
        if (empty($headerContent)) {
            return static::throwExceptionOrReturnNull($throwExceptions, sprintf("Signature::extractDescriptionFromHeaders() authorization header '%s' not found or empty", $headerName));
        }
        $description = static::extractDescriptionFromHeaderContent($headerContent, $encrypter, $throwExceptions);
        return $description;
    }


    public static function extractSignatureTypeFromDescription(array $description)
    {
        return $description['signatureType'];
    }

    public static function extractTokenFromDescription(array $description)
    {
        $signatureType = static::extractSignatureTypeFromDescription($description);
        switch ($signatureType) {
            case self::TYPE_SUBSCRIBO_BASIC:
                return $description['subscriboBasicToken'];
            case self::TYPE_SUBSCRIBO_DIGEST:
                return $description['subscriboDigestToken'];
        }
        return null;
    }


    public static function verifyDescription(array $description, TokenRing $tokenRing, array $data = array(), array $options = array(), $throwExceptions = false)
    {
        $signatureType = static::extractSignatureTypeFromDescription($description);
        switch ($signatureType) {
            case self::TYPE_SUBSCRIBO_BASIC:
                if ($tokenRing->basicToken !== static::extractTokenFromDescription($description)) {
                    return static::throwExceptionOrReturnFalse($throwExceptions, 'Subscription::verifyDescription() SubscriboBasic: provided token is different than basicToken in TokenRing');
                }
                return true;
            case self::TYPE_SUBSCRIBO_DIGEST:
                return static::verifyDigestDescription($description, $tokenRing, $data, $options, $throwExceptions);
        }
        return static::throwExceptionOrReturnFalse($throwExceptions, sprintf("Subscription::verifyDescription() Unrecognized SignatureType '%s'", $signatureType));
    }


    protected static function extractDescriptionFromHeaderContent($headerContent, Encrypter $encrypter = null, $throwExceptions)
    {
        $headerContent = trim($headerContent);
        $parts = explode(' ', $headerContent, 2);
        $signatureType = array_shift($parts);
        if (empty($signatureType)) {
            return static::throwExceptionOrReturnNull($throwExceptions, "Signature::extractDescriptionFromHeaderContent() signature type empty");
        }
        $data = array_shift($parts);
        $data = trim($data);
        switch ($signatureType) {
            case self::TYPE_SUBSCRIBO_BASIC:
                return static::assembleBasicDescription($data);
            case self::TYPE_SUBSCRIBO_DIGEST:
                $result = static::decode($data, $encrypter, $throwExceptions);
                if (( ! is_array($result)) or empty($result['signatureType'])) {
                    return static::throwExceptionOrReturnNull($throwExceptions, "Signature::extractDescriptionFromHeaderContent() signature has not been decoded properly or is invalid");
                }
                if (self::TYPE_SUBSCRIBO_DIGEST !== $result['signatureType']) {
                    return static::throwExceptionOrReturnNull($throwExceptions, "Signature::extractDescriptionFromHeaderContent() signature type from header does not agree with signatureType in description");
                }
                return $result;
        }
        return static::throwExceptionOrReturnNull($throwExceptions, sprintf("Signature::extractDescriptionFromHeaderContent() signature type '%s' not recognized", $signatureType));
    }


    protected static function verifyDigestDescription(array $description, TokenRing $tokenRing, array $data = array(), array $options = array(), $throwExceptions = false)
    {
        if (self::TYPE_SUBSCRIBO_DIGEST  !== $description['signatureType']) {
            return static::throwExceptionOrReturnFalse($throwExceptions, sprintf("Signature::verifyDigestDescription() signatureType in description ('%s') is different than required '%s'", $description['signatureType'], self::TYPE_SUBSCRIBO_DIGEST));
        }
        if (empty($description['signatureVersion'])) {
            return static::throwExceptionOrReturnFalse($throwExceptions, "Signature::verifyDigestDescription() signatureVersion missing");
        }
        if (empty($description['nonce'])) {
            return static::throwExceptionOrReturnFalse($throwExceptions, "Signature::verifyDigestDescription() nonce missing");
        }
        if (empty($description['salt'])) {
            return static::throwExceptionOrReturnFalse($throwExceptions, "Signature::verifyDigestDescription() salt missing");
        }
        if (empty($description['timestamp'])) {
            return static::throwExceptionOrReturnFalse($throwExceptions, "Signature::verifyDigestDescription() timestamp missing");
        }
        if ( ! empty($options['nonce'])) {
            if ($options['nonce'] !== $description['nonce']) {
                return static::throwExceptionOrReturnFalse($throwExceptions, sprintf("Signature::verifyDigestDescription() nonce in description ('%s') is different than nonce in options ('%s')", $description['nonce'], $options['nonce']));
            }
        }
        if (empty($description['subscriboDigestToken'])) {
            return static::throwExceptionOrReturnFalse($throwExceptions, "Signature::verifyDigestDescription() subscriboDigestToken missing");
        }
        if ($description['subscriboDigestToken'] !== $tokenRing->digestToken) {
            return static::throwExceptionOrReturnFalse($throwExceptions, sprintf("Signature::verifyDigestDescription() subscriboDigestToken in description ('%s') is different than digestToken in TokenRing ('%s')", $description['subscriboDigestToken'], $tokenRing->digestToken));
        }
        if (empty($description['signature'])) {
            return static::throwExceptionOrReturnFalse($throwExceptions, "Signature::verifyDigestDescription() signature missing");
        }
        $descriptionSignature = $description['signature'];
        $descriptionCopy = $description;
        unset($descriptionCopy['signature']);
        $dataToUse = [];
        if ( ! empty($description['dataKeys'])) {
            foreach($description['dataKeys'] as $key) {
                if ( ! array_key_exists($key, $data)) {
                    return static::throwExceptionOrReturnFalse($throwExceptions, "Signature::verifyDigestDescription() missing data with key '%s'", $key);
                }
                $dataToUse[$key] = $data[$key];
            }
        }
        $arrayToHash = ['description' => $descriptionCopy, 'data' => $dataToUse];
        $recomputedSignature = static::computeSignature($arrayToHash, $tokenRing->digestSecret);
        if ($recomputedSignature !== $descriptionSignature) {
            return static::throwExceptionOrReturnFalse($throwExceptions, "Signature::verifyDigestDescription() provided signature is different than recomputed signature");
        }
        return true;
    }


    /**
     * @param TokenRing $tokenRing
     * @param array $data
     * @param SignatureOptions|array $options
     * @param Encrypter $encrypter
     * @param array $description
     * @return string
     * @throws Exceptions\InvalidArgumentException
     */
    protected static function assembleAuthorizationHeaderContent(TokenRing $tokenRing, array $data = array(), $options = array(), Encrypter $encrypter = null, array &$description = array())
    {
        $options = ($options instanceof SignatureOptions) ? $options : new SignatureOptions($options);
        $signatureType = $options->signatureType ?: $tokenRing->ascertainType();
        if (empty($signatureType)) {
            throw new InvalidArgumentException('Signature type not provided');
        }
        if ( ! $tokenRing->check($signatureType)) {
            throw new InvalidArgumentException("We do not have enough information for Signature type or Signature type is not recognized (signatureType: '%s').", $signatureType);
        }
        switch ($signatureType) {
            case self::TYPE_SUBSCRIBO_BASIC:
                $description = static::assembleBasicDescription($tokenRing->basicToken);
                $result = self::TYPE_SUBSCRIBO_BASIC.' '.$tokenRing->basicToken;
                return $result;
            case self::TYPE_SUBSCRIBO_DIGEST:
                $description = static::assembleDigestDescription($tokenRing, $data, $options);
                $encoded = static::encode($description, $encrypter);
                $result = self::TYPE_SUBSCRIBO_DIGEST.' '.$encoded;
                return $result;
            default:
                throw new InvalidArgumentException(sprintf("Unrecognized signatureType '%s'", $signatureType));
        }
    }

    protected static function assembleDigestDescription(TokenRing $tokenRing, array $data, SignatureOptions $options)
    {
        $description = static::assembleDescriptionBase($tokenRing, $options, self::TYPE_SUBSCRIBO_DIGEST);
        $description['dataKeys'] = $options->dataKeys ?: array_keys($data);
        $arrayToHash = ['description' => $description, 'data' => $data];
        $description['signature'] = static::computeSignature($arrayToHash, $tokenRing->digestSecret);
        return $description;
    }

    protected static function computeSignature(array $data, $secret)
    {
        $stringToHash = json_encode($data);
        $signature = hash_hmac(static::HASH_HMAC_ALGORITHM, $stringToHash, $secret);
        return $signature;
    }

    protected static function assembleDescriptionBase(TokenRing $tokenRing, SignatureOptions $options, $defaultType)
    {
        $description = [
            'signatureType' => $options->signatureType ?: $defaultType,
            'signatureVersion' => $options->signatureVersion ?: '1.0',
            'subscriboDigestToken' => $tokenRing->digestToken,
            'salt' => static::generateSalt(),
            'nonce' => $options->nonce ?: static::generateNonce(),
            'timestamp' => $options->timestamp ?: static::generateTimestamp(),
        ];
        foreach ($options->getDescriptionAdds() as $key => $value) {
            $description[$key] = $value;
        }
        return $description;
    }

    protected static function assembleBasicDescription($token)
    {
        $description = [
            'signatureType' => self::TYPE_SUBSCRIBO_BASIC,
            'signatureVersion' => '1.0',
            'subscriboBasicToken' => $token,
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

    protected static function decode($data, Encrypter $encrypter = null, $throwExceptions = false)
    {
        if (empty($data)) {
            return static::throwExceptionOrReturnNull($throwExceptions, 'Signature::decode() provided data empty');
        }
        $urlDecoded = urldecode($data);
        if (empty($urlDecoded)) {
            return static::throwExceptionOrReturnNull($throwExceptions, 'Signature::decode() url decoded data empty');
        }
        $decoded = base64_decode($urlDecoded, true);
        if (empty($decoded)) {
            return static::throwExceptionOrReturnNull($throwExceptions, 'Signature::decode() base 64 decoded data empty');
        }
        if ($encrypter) {
            try {
                $decoded =  $encrypter->decrypt($decoded);
            } catch (Exception $e) {
                return static::throwExceptionOrReturnNull($throwExceptions, 'Signature::decode() exception thrown during data decryption', 110, $e);
            }
        }
        if (empty($decoded)) {
            return static::throwExceptionOrReturnNull($throwExceptions, 'Signature::decode() decrypted data empty');
        }
        $parsed = json_decode($decoded, true);
        if (empty($parsed)) {
            return static::throwExceptionOrReturnNull($throwExceptions, 'Signature::decode() json parsing failed', json_last_error());
        }
        return $parsed;
    }

    protected static function throwExceptionOrReturnNull($throwException, $message = '', $code = 0, Exception $previous = null)
    {
        if (false === $throwException) {
            return null;
        }
        $e = new InvalidArgumentException($message, $code, $previous);
        throw $e;
    }

    protected static function throwExceptionOrReturnFalse($throwException, $message = '', $code = 0, Exception $previous = null)
    {
        if (false === $throwException) {
            return false;
        }
        $e = new InvalidArgumentException($message, $code, $previous);
        throw $e;
    }

    protected static function generateSalt($length = 32)
    {
        return Str::random($length);
    }

    protected static function generateNonce($length = 32)
    {
        return Str::random($length);
    }

    protected static function generateTimestamp()
    {
        $microTimeString = microtime();
        $microTimeParts = explode(' ', $microTimeString);
        $microseconds = empty($microTimeParts[1]) ? null : $microTimeParts[1];
        $current = date('Y-m-d H:i:s');
        $result = $microseconds ? ($current.'.'.$microseconds) : $current;
        return $result;
    }

}
