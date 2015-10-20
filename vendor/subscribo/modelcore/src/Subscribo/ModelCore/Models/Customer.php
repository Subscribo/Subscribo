<?php namespace Subscribo\ModelCore\Models;

/**
 * Model Customer
 *
 * Model class for being changed and used in the application
 */
class Customer extends \Subscribo\ModelCore\Bases\Customer {

    /**
     * @param string|$email
     * @return \Illuminate\Database\Eloquent\Collection|Customer[]
     */
    public static function findAllByEmail($email)
    {
        $query = static::query()->where('email', $email);
        $result = $query->get();
        return $result;
    }

    /**
     * @param array|Contact $contact
     * @return Contact|null
     */
    public function addContactIfNeeded($contact)
    {
        if (is_array($contact) and ! Contact::dataContainsContact($contact)) {

            return null;
        }
        $existingContact = $this->alreadyHaveContact($contact);
        if ($existingContact) {

            return $existingContact;
        }
        if ($contact instanceof Contact) {
            $contact->customer()->associate($this);
            $contact->save();

            return $contact;
        }

        return Contact::generate($contact, null, null, $this);
    }

    /**
     * @param array|Contact $contact
     * @return bool|Contact
     */
    public function alreadyHaveContact($contact)
    {
        foreach ($this->contacts as $customerContact) {
            if ($customerContact->dataContainsSimilarContact($contact)) {

                return $customerContact;
            }
        }

        return false;
    }

    /**
     * @return int|null
     * @deprecated
     * @todo remove
     */
    public function getDefaultShippingAddressId()
    {
        return $this->customerConfiguration->defaultShippingAddressId;
    }

    /**
     * @return int|null
     * @deprecated
     * @todo remove
     */
    public function getDefaultBillingAddressId()
    {
        $billingDetail = $this->customerConfiguration->defaultBillingDetail;

        return $billingDetail ? $billingDetail->addressId : null;
    }
}
