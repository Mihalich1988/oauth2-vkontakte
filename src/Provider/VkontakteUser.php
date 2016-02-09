<?php

namespace Mihalich1988\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class VkontakteUser implements ResourceOwnerInterface
{
    /**
     * @var array
     */
    protected $data;

    /**
     * @param  array $response
     */
    public function __construct(array $response)
    {
        $user = $response[0];

        $this->data = $user;

        if (!empty($user['city']['title'])) {
            $this->data['city'] = $user['city']['title'];
        }
    }

    /**
     * Returns the ID for the user as a string if present.
     *
     * @return string|null
     */
    public function getId()
    {
        return $this->getField('id');
    }

    /**
     * Returns the name for the user as a string if present.
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->getFirstName() . ' ' . $this->getLastName();
    }

    /**
     * Returns the first name for the user as a string if present.
     *
     * @return string|null
     */
    public function getFirstName()
    {
        return $this->getField('first_name');
    }

    /**
     * Returns the last name for the user as a string if present.
     *
     * @return string|null
     */
    public function getLastName()
    {
        return $this->getField('last_name');
    }

    /**
     * Returns the email for the user as a string if present.
     *
     * @return string|null
     */
    public function getEmail()
    {
        return $this->getField('email');
    }

    /**
     * Returns the current location of the user as an array.
     *
     * @return array|null
     */
    public function getHometown()
    {
        return $this->getField('city');
    }

    /**
     * Returns the profile picture of the user as a string if present.
     *
     * @return string|null
     */
    public function getPictureUrl()
    {
        return $this->getField('photo_max_orig');
    }

    /**
     * Returns the gender for the user as a string if present.
     *
     * @return string|null
     */
    public function getGender()
    {
        return $this->getField('sex');
    }


    /**
     * Returns all the data obtained about the user.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * Returns a field from the Graph node data.
     *
     * @param string $key
     *
     * @return mixed|null
     */
    private function getField($key)
    {
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }
}
