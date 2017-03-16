<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Product
 *
 * @ORM\Table(name="product")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProductRepository")
 */
class Product implements \JsonSerializable
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
     * @ORM\Column(name="title", type="text")
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="unit", type="text", nullable=true)
     */
    private $unit;

    /**
     * @ORM\Column(name="price_min", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $price_min;

    /**
     * @ORM\Column(name="price_max", type="decimal", precision=10, scale=0, nullable=true)
     */
    private $price_max;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\ProductCategory", inversedBy="products")
     * @ORM\JoinColumn(name="product_category_id", referencedColumnName="id")
     */
    private $productCategory;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\PortfolioSet", mappedBy="productCategory")
     */
    private $portfolioSets;


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
     * @return Product
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
     * Set unit
     *
     * @param string $unit
     *
     * @return Product
     */
    public function setUnit($unit)
    {
        $this->unit = $unit;

        return $this;
    }

    /**
     * Get unit
     *
     * @return string
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * Set price
     *
     * @param string $price
     *
     * @return Product
     */
    public function setPrice($price)
    {
        $this->price_min = $price;
        $this->price_max = $price;

        return $this;
    }

    /**
     * Set productCategory
     *
     * @param \AppBundle\Entity\ProductCategory $productCategory
     *
     * @return Product
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

    /**
     * Set priceMin
     *
     * @param string $priceMin
     *
     * @return Product
     */
    public function setPriceMin($priceMin)
    {
        $this->price_min = $priceMin;

        return $this;
    }

    /**
     * Get priceMin
     *
     * @return string
     */
    public function getPriceMin()
    {
        return $this->price_min;
    }

    /**
     * Set priceMax
     *
     * @param string $priceMax
     *
     * @return Product
     */
    public function setPriceMax($priceMax)
    {
        $this->price_max = $priceMax;

        return $this;
    }

    /**
     * Get priceMax
     *
     * @return string
     */
    public function getPriceMax()
    {
        return $this->price_max;
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
            "category_id" => $this->getProductCategory()->getId(),
            "price" => $this->getPriceMax() ?: $this->getPriceMin(),
            "unit" => $this->getUnit(),
        ];
    }

    function __toString()
    {
        return $this->getTitle();
    }


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->portfolioSets = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add portfolioSet
     *
     * @param \AppBundle\Entity\PortfolioSet $portfolioSet
     *
     * @return Product
     */
    public function addPortfolioSet(\AppBundle\Entity\PortfolioSet $portfolioSet)
    {
        $this->portfolioSets[] = $portfolioSet;

        return $this;
    }

    /**
     * Remove portfolioSet
     *
     * @param \AppBundle\Entity\PortfolioSet $portfolioSet
     */
    public function removePortfolioSet(\AppBundle\Entity\PortfolioSet $portfolioSet)
    {
        $this->portfolioSets->removeElement($portfolioSet);
    }

    /**
     * Get portfolioSets
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPortfolioSets()
    {
        return $this->portfolioSets;
    }
}
