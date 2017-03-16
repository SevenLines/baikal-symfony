<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PortfolioSet
 *
 * @ORM\Table(name="portfolio_set")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PortfolioSetRepository")
 */
class PortfolioSet
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
     * @ORM\Column(name="title", type="text", nullable=true)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="location", type="text", nullable=true)
     */
    private $location;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\PortfolioImage", mappedBy="portfolioSet")
     */
    private $images;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ProductCategory", inversedBy="portfolioSets")
     * @ORM\JoinColumn(name="product_category_id", referencedColumnName="id")
     */
    private $productCategory;

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
     * @return PortfolioSet
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
     * Set location
     *
     * @param string $location
     *
     * @return PortfolioSet
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->images = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add image
     *
     * @param \AppBundle\Entity\PortfolioImage $image
     *
     * @return PortfolioSet
     */
    public function addImage(\AppBundle\Entity\PortfolioImage $image)
    {
        $this->images[] = $image;

        return $this;
    }

    /**
     * Remove image
     *
     * @param \AppBundle\Entity\PortfolioImage $image
     */
    public function removeImage(\AppBundle\Entity\PortfolioImage $image)
    {
        $this->images->removeElement($image);
    }

    /**
     * Get images
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * Set productCategory
     *
     * @param \AppBundle\Entity\ProductCategory $productCategory
     *
     * @return PortfolioSet
     */
    public function setProductCategory(\AppBundle\Entity\ProductCategory $productCategory = null)
    {
        $this->productCategory = $productCategory;

        return $this;
    }

    /**
     * Get productCategory
     *
     * @return \AppBundle\Entity\ProductCategory
     */
    public function getProductCategory()
    {
        return $this->productCategory;
    }
}
