<?php namespace Subscribo\RestCommon;

use Subscribo\RestCommon\Exceptions\InvalidArgumentException;
use Subscribo\RestCommon\Signature;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

class TokenRing implements Arrayable, Jsonable
{
    const FORMAT_SIMPLE = 'FORMAT_SIMPLE';
    const FORMAT_ARRAY = 'FORMAT_ARRAY';
    const FORMAT_JSON = 'FORMAT_JSON';
    const DEFAULT_FORMAT = self::FORMAT_SIMPLE;

    public $commonSecret;
    public $basicToken;
    public $digestToken;
    public $digestSecret;
    public $preferredType;

    /**
     * @param $tokenRing
     * @return TokenRing
     */
    public static function make($tokenRing)
    {
        if ($tokenRing instanceof self) {
            return $tokenRing;
        }
        return new self($tokenRing);
    }

    /**
     * @param string|array|null $data
     */
    public function __construct($data = null)
    {
        if ( ! empty($data))
        {
            $this->load(self::analyse($data));
        }
    }

    /**
     * @param array|string $data
     * @return array
     * @throws Exceptions\InvalidArgumentException
     */
    public static function analyse($data)
    {
        if (is_array($data))
        {
            return $data;
        }
        if ( ! is_string($data)) {
            throw new InvalidArgumentException('TokenRing::analyse() data provided should be array or string');
        }
        if (0 === strpos($data, 'simple_')) {
            $decoded = base64_decode(substr($data,7), true);
            if (empty($decoded)) {
                throw new InvalidArgumentException('TokenRing::analyse() provided string is invalid');
            }
            $parsed = json_decode($decoded, true);
            if (empty($parsed)) {
                throw new InvalidArgumentException('TokenRing::analyse() provided data contain invalid json');
            }
            return $parsed;
        }
        throw new InvalidArgumentException('TokenRing::analyse() data provided in unrecognized format');
    }

    /**
     * @param array $settings
     */
    public function load(array $settings)
    {
        if (array_key_exists('commonSecret', $settings)) {
            $this->commonSecret = $settings['commonSecret'];
        }
        if (array_key_exists('basicToken', $settings)) {
            $this->basicToken = $settings['basicToken'];
        }
        if (array_key_exists('digestToken', $settings)) {
            $this->digestToken = $settings['digestToken'];
        }
        if (array_key_exists('digestSecret', $settings)) {
            $this->digestSecret = $settings['digestSecret'];
        }
        if (array_key_exists('preferredType', $settings)) {
            $this->preferredType = $settings['preferredType'];
        }
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $result = [];
        if (isset($this->commonSecret)) {
            $result['commonSecret'] = $this->commonSecret;
        }
        if (isset($this->basicToken)) {
            $result['basicToken'] = $this->basicToken;
        }
        if (isset($this->digestToken)) {
            $result['digestToken'] = $this->digestToken;
        }
        if (isset($this->digestSecret)) {
            $result['digestSecret'] = $this->digestSecret;
        }
        if (isset($this->preferredType)) {
            $result['preferredType'] = $this->preferredType;
        }
        return $result;
    }

    /**
     * @param int $options
     * @return string
     */
    public function toJson($options = 0)
    {
        $data = $this->toArray();
        $result = json_encode($data, $options);
        return $result;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->export();
    }

    /**
     * @param string|null $format
     * @return array|string
     * @throws Exceptions\InvalidArgumentException
     */
    public function export($format = null)
    {
        if (is_null($format)) {
            $format = self::DEFAULT_FORMAT;
        }
        switch ($format) {
            case self::FORMAT_ARRAY:
                return $this->toArray();
            case self::FORMAT_JSON:
                return $this->toJson();
            case self::FORMAT_SIMPLE:
                $json = $this->toJson();
                $result = 'simple_'.base64_encode($json);
                return $result;
        }
        throw new InvalidArgumentException(sprintf("TokenRing::export unrecognized format '%s'", $format));
    }

    /**
     * @return string|null
     */
    public function ascertainType()
    {
        if ( ! empty($this->preferredType)) {
            return $this->preferredType;
        }
        if (( ! empty($this->digestToken)) and ( ! empty($this->digestSecret))){
            return Signature::TYPE_SUBSCRIBO_DIGEST;
        }
        if ( ! empty($this->basicToken)) {
            return Signature::TYPE_SUBSCRIBO_BASIC;
        }
        return null;
    }

    /**
     * Checks, whether we have enough information for given type and whether type is recognized
     *
     * @param string $type
     * @return bool
     */
    public function check($type)
    {
        switch ($type)
        {
            case Signature::TYPE_SUBSCRIBO_BASIC:
                if (empty($this->basicToken)) {
                    return false;
                }
                return true;
            case Signature::TYPE_SUBSCRIBO_DIGEST:
                if (empty($this->digestToken)) {
                    return false;
                }
                if (empty($this->digestSecret)) {
                    return false;
                }
                return true;
            default:
                return false;
        }
    }
}
