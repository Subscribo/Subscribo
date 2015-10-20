<?php

namespace Subscribo\ModelCore\Models;

use Illuminate\Support\Arr;

/**
 * Model Contact
 *
 * Model class for being changed and used in the application
 */
class Contact extends \Subscribo\ModelCore\Bases\Contact
{
    /**
     * @param array $data
     * @param Person $person
     * @param Address $address
     * @param Customer $customer
     * @return Contact
     */
    public static function generate(array $data, Person $person = null, Address $address = null, Customer $customer = null)
    {
        $person = $person ?: Person::generate($data);
        $address = $address ?: Address::generate($data, $person, $customer);
        $instance = static::make($data, $person, $address, $customer);
        $instance->save();

        return $instance;
    }

    /**
     * @param array $data
     * @param Person $person
     * @param Address $address
     * @param Customer $customer
     * @return Contact
     * @throws \InvalidArgumentException
     */
    public static function make(array $data, Person $person = null, Address $address = null, Customer $customer = null)
    {
        $instance = new Contact();
        $instance->fill(array_intersect_key($data, array_flip($instance->getFillable())));
        $person = $person ?: Person::make($data);
        $address = $address ?: Address::make($data, $person, $customer);


        $instance->person()->associate($person);
        $instance->address()->associate($address);
        $instance->customer()->associate($customer);
        if ($customer and is_null($instance->email)) {
            $instance->email = $customer->email;
        }

        $instance->refreshDescriptor();

        return $instance;
    }

    /**
     * @param Service|int$service
     * @param Contact|int|null $contact
     * @return null|Contact
     */
    public static function provideForService($service, $contact)
    {
        if (is_null($contact)) {

            return null;
        }
        $instance = ($contact instanceof Contact) ? $contact : static::find($contact);
        if (empty($instance)) {

            return null;
        }
        $serviceId = ($service instanceof Service) ? $service->id : $service;
        if ($instance->serviceId === $serviceId) {

            return $instance;
        }

        return $instance->replicateForService($service);
    }

    /**
     * @param Service|int $service
     * @param Person $person
     * @param Address $address
     * @return Contact
     */
    public function replicateForService($service, Person $person = null, Address $address = null)
    {
        $attributes = Arr::except($this->attributes, $this->getExceptColumns());
        $newInstance = new static();
        $newInstance->setRawAttributes($attributes);
        $newInstance->preimage()->associate($this);
        $newInstance->service()->associate($service);
        if ($this->personId and ! $person) {
            $person = $this->person->replicateForService($service);
        }
        if ($this->addressId and ! $address) {
            $address = $this->address->replicateForService($service, $person);
        }
        if ($person) {
            $newInstance->person()->associate($person);
        if ($address)
            $newInstance->address()->associate($address);
        }
        $newInstance->save();

        return $newInstance;
    }

    public static function dataContainsContact(array $data)
    {
        if (Address::dataContainsAddress($data)) {

            return true;
        }
        if (static::dataContainsPhone($data)) {

            return true;
        }
        return ! empty($data['email']);
    }

    /**
     * @param array $data
     * @return bool
     */
    public static function dataContainsPhone(array $data)
    {
        return (( ! empty($data['phone'])) or ( ! empty($data['mobile'])));
    }

    /**
     * @param Contact|array $data
     * @return bool
     */
    public function dataContainsSimilarContact($data)
    {
        $contact = ($data instanceof Contact) ? $data : static::make($data);

        return $this->contactsAreSimilar($contact);
    }

    /**
     * @param Contact $contact
     * @return bool
     */
    public function contactsAreSimilar(Contact $contact)
    {
        if ($this->address and ! $this->address->addressesAreSimilar($contact->address)) {

            return false;
        }
        if ($this->person and ! $this->person->personsAreSimilar($contact->person)) {

            return false;
        }
        if ($contact->address and ! $this->address) {

            return false;
        }
        if ($contact->person and ! $this->person) {

            return false;
        }
        $exceptions = $this->getExceptColumns();
        $exportedThis = Arr::except($this->attributesToArray(), $exceptions);
        $exportedThat = Arr::except($contact->attributesToArray(), $exceptions);

        return $exportedThis == $exportedThat;
    }

    /**
     * @return $this
     */
    public function refreshDescriptor()
    {
        $name = $this->person ? $this->person->name : null;
        $descriptor = ($name) ? $name.', ' : '';
        if ($this->address) {
            $streetLine = $this->address->compileStreetLine();
            $descriptor .= $streetLine ? $streetLine.', ': '';
            $descriptor .= $this->address->city.', ';
            $descriptor .= $this->address->country->identifier;
        }
        if ($this->email) {
            $descriptor = $descriptor ? ($descriptor.'; '.$this->email) : $this->email;
        }
        if ($this->phone) {
            $descriptor = $descriptor ? ($descriptor.'; '.$this->phone) : $this->phone;
        }
        if ($this->mobile) {
            $descriptor = $descriptor ? ($descriptor.'; '.$this->mobile) : $this->mobile;
        }

        $this->descriptor = $descriptor;

        return $this;
    }

    /**
     * @return array
     */
    private function getExceptColumns()
    {
        return [
            $this->getKeyName(),
            $this->getCreatedAtColumn(),
            $this->getUpdatedAtColumn(),
            'preimage_id',
            'customer_id',
            'service_id',
            'person_id',
            'address_id',
        ];
    }
}
