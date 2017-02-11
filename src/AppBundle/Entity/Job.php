<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Job
 *
 * @ORM\Table(name="jobs")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\JobRepository")
 */
class Job implements \JsonSerializable
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
     * @ORM\Column(name="title", type="text", unique=true)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=True)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="short_description", type="text", nullable=True)
     */
    private $shortDescription;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\ProductCategory", mappedBy="job")
     */
    private $productCategories;

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
     * Set title
     *
     * @param string $title
     *
     * @return Job
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Job
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
     * Set shortDescription
     *
     * @param string $shortDescription
     *
     * @return Job
     */
    public function setShortDescription($shortDescription)
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }

    /**
     * Get shortDescription
     *
     * @return string
     */
    public function getShortDescription()
    {
        return $this->shortDescription;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    function jsonSerialize()
    {
        return [
            "title" => $this->getTitle(),
            "description" => $this->getDescription(),
            "short_description" => $this->getShortDescription(),
            "id" => $this->getId(),
        ];
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->productCategories = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add productCategory
     *
     * @param \AppBundle\Entity\ProductCategory $productCategory
     *
     * @return Job
     */
    public function addProductCategory(\AppBundle\Entity\ProductCategory $productCategory)
    {
        $this->productCategories[] = $productCategory;

        return $this;
    }

    /**
     * Remove productCategory
     *
     * @param \AppBundle\Entity\ProductCategory $productCategory
     */
    public function removeProductCategory(\AppBundle\Entity\ProductCategory $productCategory)
    {
        $this->productCategories->removeElement($productCategory);
    }

    /**
     * Get productCategories
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProductCategories()
    {
        return $this->productCategories;
    }
}
