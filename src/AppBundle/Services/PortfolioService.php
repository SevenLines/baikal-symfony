<?php
/**
 * Created by PhpStorm.
 * User: m
 * Date: 21.03.17
 * Time: 20:57
 */

namespace AppBundle\Services;


use AppBundle\Entity\PortfolioImage;
use AppBundle\Entity\ProductCategory;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Vich\UploaderBundle\Templating\Helper\UploaderHelper;

class PortfolioService
{
    private $vich;
    private $cacheManager;
    /**
     * @var Router
     */
    private $router;

    public function __construct(UploaderHelper $vich, CacheManager $cacheManager, Router $router)
    {
        $this->vich = $vich;
        $this->cacheManager = $cacheManager;
        $this->router = $router;
    }


    public function toDict(PortfolioImage $image)
    {
        $path = $this->vich->asset($image, 'imageFile');
        return [
            "id" => $image->getId(),
            "src_big_thumb" => $this->cacheManager->getBrowserPath($path, "big_thumb"),
            "src_thumb" => $this->cacheManager->getBrowserPath($path, "thumb"),
            "delete_url" => $this->router->generate("api_portfolio_delete", [
                "id" => $image->getId(),
            ]),
            "update_url" => $this->router->generate("api_portfolio_patch", [
                "id" => $image->getId(),
            ]),
            "categories" => array_map(function (ProductCategory $category) {
                return [
                    'id' => $category->getId(),
                    'title' => $category->getTitle()
                ];
            }, $image->getCategories()->toArray())
        ];
    }

}