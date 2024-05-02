<?php

namespace App\DTO;

use App\Entity\Property;
use App\Validator\VerifyIsAuthuserEmail;
use App\Validator\VerifyIsAuthuserUsername;
use Symfony\Component\Validator\Constraints as Assert;

class ContactDTO
{

    /**
     * @var Property|null
     */
    private ?Property $property = null;

    /**
     * @var string
     */
    #[Assert\NotBlank()]
    #[Assert\Length(min:3)]
    /* #[VerifyIsAuthuserUsername()] */
    private string $firstname;

    /**
     * @var string|null
     */
    /* #[Assert\NotBlank()] */
    #[Assert\Length(min:3)]
    private ?string $lastname = null;

    /**
     * @var string
     */
    #[Assert\NotBlank()]
    #[Assert\Email()]
    #[Assert\Length(min:3)]
    /* #[VerifyIsAuthuserEmail()] */
    private string $email;

    /**
     * @var string
     */
    #[Assert\NotBlank()]
    private string $phone;

    /**
     * @var string
     */
    #[Assert\NotBlank()]
    #[Assert\Length(min:10)]
    private string $message;


    /**
     * Get the value of property
     */ 
    /**
     * @return Property|null
     */
    public function getProperty() : ?Property
    {
        return $this->property;
    }

    /**
     * Set the value of property
     *
     * @return  self
     */ 
    /**
     * @param Property|null $property
     * @return static|null
     */
    public function setProperty(?Property $property) : ?static
    {
        $this->property = $property;

        return $this;
    }

    /**
     * Get the value of firstname
     */ 
    public function getFirstname() : ?string
    {
        return $this->firstname;
    }

    /**
     * Set the value of firstname
     *
     * @return  self
     */ 
    public function setFirstname(?string $firstname) : ?static
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get the value of lastname
     */ 
    public function getLastname() : ?string
    {
        return $this->lastname;
    }

    /**
     * Set the value of lastname
     *
     * @return  self
     */ 
    public function setLastname(?string $lastname) : ?static
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get the value of email
     */ 
    public function getEmail() : ?string
    {
        return $this->email;
    }

    /**
     * Set the value of email
     *
     * @return  self
     */ 
    public function setEmail(?string $email) : static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of phone
     */ 
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * Set the value of phone
     *
     * @return  self
     */ 
    public function setPhone(?string $phone) : ?static
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get the value of message
     */ 
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * Set the value of message
     *
     * @return  self
     */ 
    public function setMessage(?string $message) : ?static
    {
        $this->message = $message;

        return $this;
    }

}