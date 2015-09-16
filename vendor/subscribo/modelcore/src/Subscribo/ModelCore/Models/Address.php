<?php

namespace Subscribo\ModelCore\Models;

use InvalidArgumentException;
use Subscribo\ModelCore\Models\Country;
use Subscribo\ModelCore\Models\Person;
use Subscribo\ModelCore\Models\Customer;

/**
 * Model Address
 *
 * Model class for being changed and used in the application
 */
class Address extends \Subscribo\ModelCore\Bases\Address
{
    /**
     * @param array $data
     * @param Person|null $person
     * @param Customer|null $customer
     * @return Address
     */
    public static function generate(array $data, Person $person = null, Customer $customer = null)
    {
        $person = $person ?: Person::generate($data);
        $instance = static::make($data, $person, $customer);
        $instance->save();

        return $instance;
    }

    /**
     * @param array $data
     * @param Person|null $person
     * @param Customer|null $customer
     * @return Address
     * @throws \InvalidArgumentException
     */
    public static function make(array $data, Person $person = null, Customer $customer = null)
    {
        $instance = new Address();
        $instance->fill(array_intersect_key($data, array_flip($instance->getFillable())));
        $country = Country::findByIdentifier($data['country']);
        if (empty($country)) {
            throw new InvalidArgumentException('Specified country not found');
        }
        $instance->country()->associate($country);
        $instance->countryUnion = $country->countryUnion;
        $person = $person ?: Person::make($data);
        $instance->person()->associate($person);
        $instance->customer()->associate($customer);

        if ($person->name) {
            $instance->salutation = static::compileSalutation($person, $country);
            $instance->personName = static::compilePersonName($person, $country);
        }
        $instance->refreshDescriptor();

        return $instance;
    }

    /**
     * @param array $data
     * @return bool
     */
    public static function dataContainsAddress(array $data)
    {
        if (empty($data['country']) or empty($data['city'])) {
            return false;
        }
        if (( ! empty($data['street']) )
            or ( ! empty($data['house']))
            or ( ! empty($data['post_code'])) ) {
            return true;
        }
        return false;
    }

    /**
     * @param array|Address $data
     * @param Customer|null $customer
     * @return \Illuminate\Database\Eloquent\Model|null|Address|static
     */
    public static function ifDataContainsAddressFindSimilarOrGenerate($data, Customer $customer = null)
    {
        if (empty($data)) {

            return null;
        }
        if (is_array($data) and ( ! static::dataContainsAddress($data))) {

            return null;
        }
        $found = static::customerHaveAddress($data, $customer);

        if ($found) {

            return $found;
        }

        if ($data instanceof Address) {
            if ($customer) {
                $data->customerId = $customer->id;
                $data->save();
            }

            return $data;
        }

        return static::generate($data, null, $customer);
    }


    /**
     * @param array|Address $data
     * @param Customer|null $customer
     * @return \Illuminate\Database\Eloquent\Model|null|static
     * @throws \InvalidArgumentException
     */
    public static function customerHaveAddress($data, Customer $customer = null)
    {
        if (empty($customer)) {

            return null;
        }
        $address = is_array($data) ? static::make($data) : $data;
        if (( ! ($address instanceof Address))) {
            throw new InvalidArgumentException('Parameter $data should be either array or Address object');
        }
        $exported = $address->attributesToArray();
        $exported['customer_id'] = $customer->id;
        $found = static::query()->where($exported)->first();

        return $found;
    }

    /**
     * @return $this
     */
    public function refreshDescriptor()
    {
        $name = $this->person ? $this->person->name : null;
        $descriptor = ($name) ? $name.', ' : '';
        $streetLine = $this->compileStreetLine();
        $descriptor .= $streetLine ? $streetLine.', ': '';
        $descriptor .= $this->city.', ';
        $descriptor .= $this->country->identifier;
        $this->descriptor = $descriptor;

        return $this;
    }

    public function compileStreetLine()
    {
        return trim($this->street.' '.$this->compileFlatDesignator());
    }

    public function compileFlatDesignator()
    {
        $parts = [];
        if ($this->house) {
            $parts[] = $this->house;
        }
        if ($this->gate) {
            $parts[] = $this->gate;
        }
        if ($this->stairway) {
            $parts[] = $this->stairway;
        }
        if ($this->floor) {
            $parts[] = $this->floor;
        }
        if ($this->apartment) {
            $parts[] = $this->apartment;
        }

        return implode('/', $parts);
    }

    /**
     * @param Person $person
     * @param Country $country
     * @return string
     */
    protected static function compileSalutation(Person $person, Country $country)
    {
        $suffix = trim(strtolower($person->suffix), '.');
        $prefix = trim(strtolower($person->prefix), '.');
        $result = '';
        switch ($country->identifier) {
            case 'AT':
            case 'DE':
                if ('man' === $person->gender) {
                    $result = 'Herrn';
                } elseif ('woman' === $person->gender) {
                    $result = 'Frau';
                } else {
                    return '';
                }
                if (('prof' === $prefix) or ('univ.prof' === $prefix)) {
                    return $result.' professor';
                }
                if (('dr' === $prefix) or ('phd' === $suffix)) {
                    return $result.' doktor';
                }
                return $result;
            case 'SK':
                if ('man' === $person->gender) {
                    $result = 'Vážený pán';
                    if (('prof' === $prefix) or ('univ.prof' === $prefix)) {
                        return $result.' profesor';
                    }
                    if (('dr' === $prefix) or ('phd' === $suffix)) {
                        return $result.' doktor';
                    }
                } elseif ('woman' === $person->gender) {
                    $result = 'Vážená pani';
                    if (('prof' === $prefix) or ('univ.prof' === $prefix)) {
                        return $result.' profesorka';
                    }
                    if (('dr' === $prefix) or ('phd' === $suffix)) {
                        return $result.' doktorka';
                    }
                }
                return $result;
            case 'CZ':
                if ('man' === $person->gender) {
                    $result = 'Vážený pan';
                    if (('prof' === $prefix) or ('univ.prof' === $prefix)) {
                        return $result.' profesor';
                    }
                    if (('dr' === $prefix) or ('phd' === $suffix)) {
                        return $result.' doktor';
                    }
                } elseif ('woman' === $person->gender) {
                    $result = 'Vážená paní';
                    if (('prof' === $prefix) or ('univ.prof' === $prefix)) {
                        return $result.' profesorka';
                    }
                    if (('dr' === $prefix) or ('phd' === $suffix)) {
                        return $result.' doktorka';
                    }
                }
                return $result;
            default:
                return '';
        }
    }

    /**
     * @param Person $person
     * @param Country $country
     * @return string
     */
    protected static function compilePersonName(Person $person, Country $country)
    {

        $result = '';
        switch ($country->identifier) {
            case 'AT':
            case 'DE':
            case 'SK':
            case 'CZ':
                break;
            default:
                if ('man' === $person->gender) {
                    $result = 'Mr ';
                } elseif ('woman' === $person->gender) {
                    $result = 'Ms ';
                }
        }

        return  $result.($person->name);
    }

}
