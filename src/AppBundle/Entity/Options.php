<?php

namespace AppBundle\Entity;

use Doctrine\Common\Persistence\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping as ORM;

/**
 * Options
 *
 * @ORM\Table(name="options")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OptionsRepository")
 */
class Options
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="phones", type="text", nullable=true)
     */
    private $phones;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="text", nullable=true)
     */
    private $email;

    /**
     * Письма куда будут отсылаться сообщения по заявкам
     * @var string
     *
     * @ORM\Column(name="manager_emails", type="text", nullable=true)
     */
    private $manager_emails;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set phones
     *
     * @param string $phones
     *
     * @return Options
     */
    public function setPhones($phones)
    {
        $this->phones = $phones;

        return $this;
    }

    /**
     * Get phones
     *
     * @return string
     */
    public function getPhones()
    {
        return $this->phones;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Options
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Options
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set managerEmails
     *
     * @param string $managerEmails
     *
     * @return Options
     */
    public function setManagerEmails($managerEmails)
    {
        $this->manager_emails = $managerEmails;

        return $this;
    }

    /**
     * Get managerEmails
     *
     * @return string
     */
    public function getManagerEmails()
    {
        return $this->manager_emails;
    }

    public function getManagerEmailsArray()
    {
        return preg_split('/[ ,;]/', $this->manager_emails);
    }
}
