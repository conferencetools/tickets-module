<?php

namespace ConferenceTools\Tickets\Domain\ValueObject;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * Class Delegate
 * @package ConferenceTools\Tickets\Domain\ValueObject
 * @ORM\Embeddable()
 */
class Delegate
{
    /**
     * @var string
     * @ORM\Column(type="string")
     * @JMS\Type("string")
     */
    private $firstname;
    /**
     * @var string
     * @ORM\Column(type="string")
     * @JMS\Type("string")
     */
    private $lastname;
    /**
     * @var string
     * @ORM\Column(type="string")
     * @JMS\Type("string")
     */
    private $email;
    /**
     * @var string
     * @ORM\Column(type="string")
     * @JMS\Type("string")
     */
    private $company;
    /**
     * @var string
     * @ORM\Column(type="string")
     * @JMS\Type("string")
     */
    private $twitter;
    /**
     * @var string
     * @ORM\Column(type="string")
     * @JMS\Type("string")
     */
    private $requirements;

    /**
     * Delegate constructor.
     * @param string $firstname
     * @param string $lastname
     * @param string $email
     * @param string $company
     * @param string $twitter
     * @param string $requirements
     */
    public function __construct(string $firstname, string $lastname, string $email, string $company, string $twitter, string $requirements)
    {
        $this->firstname = $firstname;
        $this->lastname = $lastname;
        $this->email = $email;
        $this->company = $company;
        $this->twitter = $twitter;
        $this->requirements = $requirements;
    }

    public static function emptyObject()
    {
        return new static('', '', '', '', '', '', '');
    }

    public static function fromArray(array $data)
    {
        return new static(
            $data['firstname'],
            $data['lastname'],
            $data['email'],
            $data['company'],
            $data['twitter'],
            $data['requirements']
        );
    }

    /**
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @return string
     */
    public function getTwitter()
    {
        return $this->twitter;
    }

    /**
     * @return string
     */
    public function getRequirements()
    {
        return $this->requirements;
    }
}
